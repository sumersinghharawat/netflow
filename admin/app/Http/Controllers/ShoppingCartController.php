<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartBankPaymentReceiptRequest;
use App\Http\Requests\cartUpdateRequest;
use App\Http\Requests\checkoutSubmitRequest;
use App\Http\Requests\OrderApprovalRequest;
use App\Http\Requests\RequestsAddnewAddress;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartPaymentReceipt;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Package;
use App\Models\PaymentGatewayConfig;
use App\Models\PinNumber;
use App\Models\User;
use App\Services\commissionService;
use App\Services\OrderService;
use App\Services\UserApproveService;
use App\Traits\UploadTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Treepath;

class ShoppingCartController extends CoreInfController
{
    protected $serviceClass;

    public function __construct(OrderService $serviceClass)
    {
        $this->serviceClass = $serviceClass;
    }

    use  UploadTraits;

    public function index()
    {
        $packages = Package::with('category')->ActiveRepurchasePackage()->paginate(6);
        $currency = currencySymbol();

        return view('admin.products.index', compact('packages', 'currency'));
    }

    public function cartIndex()
    {
        $products = Cart::with('packageDetails')->User()->get();
        $currency = currencySymbol();

        return view('admin.cart.index', compact('products', 'currency'));
    }

    public function productDetails($id)
    {
        $package = Package::with('category')->ActiveRepurchasePackage()->where('id', $id)->first();
        $product = Cart::with('packageDetails')->User()->where('package_id', $id)->first();
        $currency = currencySymbol();

        return view('admin.products.product-detail', compact('package', 'product', 'currency'));
    }

    /** Add to Cart Section */
    public function addToCart($package_id)
    {
        $cart = Cart::where('package_id', $package_id)->User()->first();
        $user = auth()->user()->load('cart');
        try {
            $qty = $cart->quantity ?? 0;
            if ($cart) {
                auth()->user()->cart()->updateExistingPivot(
                    $package_id,
                    ['quantity' => $qty + 1]
                );
            } else {
                auth()->user()->cart()->attach([$package_id => [
                    'quantity' => 1,
                ]]);
            }

            return redirect(route('cart.view'))->with('success', 'product added to cart succesfully');
        } catch (Throwable $e) {
            return redirect(route('products.view'))
                ->with('error', $e->getMessage());
        }
    }
    /** Add to Cart Section End*/

    /** Update Quantity Cart Section */
    public function cartUpdate(cartUpdateRequest $request)
    {
        $data = $request->validated();
        try {
            $cart = Cart::where('package_id', $request->package_id)->User()->first();
            $package = Package::where('id', $request->package_id)->first();
            $total_price = number_format((float) ($package->price * $data['quantity']), 2, '.', '');
            $user = auth()->user()->load('cart');
            if ($cart) {
                auth()->user()->cart()->updateExistingPivot($request->package_id, ['quantity' => $data['quantity']]);
            } else {
                auth()->user()->cart()->attach([$request->package_id => [
                    'quantity' => 1,
                ]]);
            }

            return response()->json(['success' => 'Cart updated successfully.', 'total_price' => $total_price]);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Error updating cart']);
        }
    }
    /** Update Quantity Cart Section End*/

    /** Cart Delete */
    public function cartDelete($package_id)
    {
        try {
            $cart = Cart::where('package_id', $package_id)->User()->delete();

            return response()->json(['success' => 'Cart deleted successfully.']);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /** Checkout Control */
    public function checkout()
    {
        /*To Do - Group PV */
        $user_address = Address::User()->get();
        $default_address = Address::User()->Default()->first();
        $cart_items = Cart::User()->with('packageDetails')->get();
        $paymentGateways = PaymentGatewayConfig::ActiveRepurchase()->get();

        $total_amount = 0;
        $total_pv = 0;
        if ($cart_items->isEmpty()) {
            return redirect(route('cart.view'))->with('error', 'Cart items not found');
        } else {
            foreach ($cart_items as $item) {
                $amount = $item->quantity * $item->packageDetails->price;
                //$pv     = $item->quantity * $item->packageDetails->bv_value;
                $pv = $item->quantity * $item->packageDetails->pair_value;
                $total_amount = $total_amount + $amount;
                $total_pv = $total_pv + $pv;
            }
            $total = $total_amount ?? 0;
            $total_pv = $total_pv ?? 0;
            if (auth()->user()->user_type == 'admin') {
                $user = auth()->user();
            } else {
                $user = User::where('user_type', 'admin')->first();
            }
            $currency = currencySymbol();

            return view('admin.cart.checkout', compact('user_address', 'cart_items', 'total', 'total_pv', 'default_address', 'paymentGateways', 'user', 'currency'));
        }
    }

    /** Checkout Add new Address */
    public function addNewAddress(RequestsAddnewAddress $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $address = new Address;
            $address->user_id = Auth::user()->id;
            $address->name = $data['name'];
            $address->address = $data['address'];
            $address->zip = $data['zip'];
            $address->city = $data['city'];
            $address->mobile = $data['mobile'];
            $address->save();
            DB::commit();
            $addresses = Address::User()->get();
            $view = view('ajax.shoppingCart.contact-address', compact('addresses'));

            return response()->json([
                'message' => 'Address Created successfully',
                'data' => $view->render(),
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }

    /** Address Delete */
    public function cartAddressDelete(Request $request, $address)
    {
        $address = Address::find($address);
        try {
            $address->delete();
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Address Successfully Deleted.',
                ]);
            } else {
                return redirect()->back()->with([
                    'status' => true,
                    'message' => 'Address Successfully Deleted.',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => true,
                'message' => 'Error occured!',
            ], 404);
        }
    }

    /** Make Default Address  */
    public function cartDefaultAddress(Request $request, $id)
    {
        try {
            $address = Address::Default()->first();
            if (isset($address)) {
                $address->update([
                    'is_default' => '0',
                ]);
            }
            $defaultAddress = Address::where('id', $id)->User()->first();
            $defaultAddress->update([
                'is_default' => '1',
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Address made default.',
                ]);
            } else {
                return redirect()->back()->with([
                    'status' => true,
                    'message' => 'Address making default error.',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => true,
                'message' => 'Error occured!',
            ], 404);
        }
    }

    /** Checkout submit to Order Details */
    public function checkoutSubmit(checkoutSubmitRequest $request)
    {

        $data                   = $request->validated();
        $currency               = currencySymbol();
        $regdata                = $request->all();
        $epinPaymentConfig      = PaymentGatewayConfig::where('slug', 'e-pin')->first();
        $ewalletPaymentConfig   = PaymentGatewayConfig::where('slug', 'e-wallet')->first();

        $address = Address::find($data['default_address']);
        if(!isset($address)){
            $defaultAddress = json_decode($data['default_address']);
            $address = Address::find($defaultAddress->id);
        }
        $cart_items = Cart::User()->with('packageDetails')->get();
        $paymentConfig = PaymentGatewayConfig::where('name', 'Bank Transfer')->first();
        $user = Auth::user();
        $prefix = str_replace("_", "", config('database.connections.mysql.prefix'));

        if ($request->payment_method == $paymentConfig->id) {
            DB::beginTransaction();
            try {
                $order = new Order;
                $order->user_id = Auth::user()->id;
                $order->order_address_id = $address->id;
                $order->order_date = Carbon::now();
                $order->total_amount = $request->totalAmount;
                $order->total_pv = $request->total_pv;
                $order->order_status = '0';
                $order->payment_method = $request->payment_method;
                $order->save();
                $invoice_no = makeInvoice($order->id);
                $order->update([
                    'invoice_no' => $invoice_no,
                ]);
                if ($order) {
                    try {
                        foreach ($cart_items as $item) {
                            $orderDetail = new OrderDetail;
                            $orderDetail->order_id = $order->id;
                            $orderDetail->package_id = $item->package_id;
                            $orderDetail->quantity = $item->quantity;
                            $orderDetail->amount = $item->quantity * $item->packageDetails->price;
                            $orderDetail->product_pv = $item->quantity * $item->packageDetails->pair_value;
                            $orderDetail->order_status = '1';
                            $orderDetail->save();
                        }
                    } catch (\Throwable $e) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Order Detail Submitting Error Occured!',
                        ], 404);
                    }
                }

                $cartPaymentReceipt = CartPaymentReceipt::where('user_id', auth()->user()->id);
                $cartPaymentReceipt->update([
                    'order_id' => $order->id,
                ]);

                $user = auth()->user();
                $user->update([
                    'personal_pv' => $user->personal_pv + $request->total_pv,
                ]);
                $user->load('sponsor');
                if ($this->moduleStatus()['product_status']) {
                    $approveService = new UserApproveService();
                    $approveService->updateGroupPV($user->sponsor, $user->personal_pv, $user->id);
                }
                if ($this->moduleStatus()['rank_status']) {
                    $commissionService = new commissionService;
                    $commissionService->updateUplineRank($request->user_id, $prefix);
                }
                //TO DO Level commission call
                DB::commit();
                if ($orderDetail) {
                    DB::beginTransaction();
                    try {
                        Cart::User()->delete();
                        DB::commit();
                    } catch (\Throwable $e) {
                        return response()->json([
                            'status' => true,
                            'message' => 'Cart Removal occured!',
                        ], 404);
                    }
                }
                $total = $request->totalAmount;

                $paymentMethod = PaymentGatewayConfig::find($request->payment_method);

                return view('admin.cart.invoice', compact('order', 'address', 'cart_items', 'total', 'paymentMethod', 'currency'))
                    ->with('success', 'Package purchased succesfully .Your Invoice number is :' . $order->invoice_no);
            } catch (\Throwable $e) {
                DB::rollback();
                if ($request->ajax()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Order Detail Submitting Error Occured!, please try again.',
                    ], 400);
                }

                return back()->with('error', $e->getMessage());
            }
        } else {
            $userApproveService = new UserApproveService();
            $paymentType = PaymentGatewayConfig::findOrfail($request->payment_method);
            $paymentStatus = $userApproveService->checkPaymentMethod($this->moduleStatus(), $paymentType, $regdata, $user, $user, 'cart');
            if ($paymentStatus) {
                try {
                    DB::beginTransaction();
                    $order = new Order;
                    $order->user_id = Auth::user()->id;

                    $order->order_address_id = $address->id;
                    $order->order_date = Carbon::now();
                    $order->total_amount = $request->totalAmount;
                    $order->total_pv = $request->total_pv;

                    if ($request->payment_method != $paymentConfig->id) {
                        $order->order_status = '1';
                    } else {
                        $order->order_status = '0';
                    }
                    $order->payment_method = $request->payment_method;
                    $order->save();

                    $invoice_no = makeInvoice($order->id);
                    $order->update([
                        'invoice_no' => $invoice_no,
                    ]);


                    $user = User::find(auth()->user()->id);
                    $user->update([
                        'personal_pv' => $user->personal_pv + $request->total_pv,
                    ]);

                    $user->load('sponsor');
                    if ($this->moduleStatus()['product_status']) {
                        $approveService = new UserApproveService();
                        $approveService->updateGroupPV($user->sponsor, $user->personal_pv, $user->id);
                    }
                    if ($this->moduleStatus()['rank_status']) {
                        $commissionService = new commissionService;
                        $commissionService->updateUplineRank($request->user_id, $prefix);
                    }
                    DB::commit();

                    if ($order) {
                        DB::beginTransaction();
                        try {
                            foreach ($cart_items as $item) {
                                $orderDetail = new OrderDetail;
                                $orderDetail->order_id = $order->id;
                                $orderDetail->package_id = $item->package_id;
                                $orderDetail->quantity = $item->quantity;
                                $orderDetail->amount = $item->quantity * $item->packageDetails->price;
                                $orderDetail->product_pv = $item->quantity * $item->packageDetails->pair_value;
                                $orderDetail->order_status = '1';
                                $orderDetail->save();
                            }
                            DB::commit();
                        } catch (\Throwable $e) {
                            return response()->json([
                                'status' => true,
                                'message' => 'Order Detail Submitting Error Occured!',
                            ], 404);
                        }
                    }
                    $orderDetail = OrderDetail::where('order_id', $order->id)->get();
                    if ($orderDetail) {
                        DB::beginTransaction();
                        try {
                            Cart::User()->delete();
                            DB::commit();
                        } catch (\Throwable $e) {
                            return response()->json([
                                'status' => true,
                                'message' => 'Cart Removal occured!',
                            ], 404);
                        }
                    }
                    $total = $request->totalAmount;
                    $paymentMethod = PaymentGatewayConfig::find($request->payment_method);
                    $currency = currencySymbol();

                    return view('admin.cart.invoice', compact('order', 'address', 'cart_items', 'total', 'paymentMethod', 'currency'))
                        ->with('success', 'Package purchased succesfully.Your Invoice number is :' . $order->invoice_no);
                } catch (\Throwable $e) {
                    dd($e->getMessage());
                    return response()->json([
                        'status' => true,
                        'message' => 'Error occured!',
                    ], 404);
                }
            }
        }
    }

    public function pendingorderApproval(Request $request)
    {
        return view('order.approval');
    }

    public function getpendingOrders(Request $request)
    {
        $order = Order::query();
        if ($request->has('userId') && $request->userId != null) {
            $order->where('user_id', $request->userId);
        }
        $pendingOrders = $order->with('user:username,id', 'user.userDetail:name,second_name,id,user_id')->Pending();
        $currency = currencySymbol();
        return DataTables::of($pendingOrders)
            ->addColumn('checkall', function ($data) {
                $checkBox = '<input type="checkbox" name="order[]" class="form-check-input checked-box" onclick="showOrderActiveActionPopup()"
                value="' . $data->id . '">';
                return $checkBox;
            })
            ->editColumn('total_amount', function ($data) use ($currency) {
                return $currency . ' ' . formatCurrency($data->total_amount);
            })
            ->editColumn('payment_method', fn ($data) => ($data->paymentMethod->name ?? 'NA'))

            ->addColumn('member', function ($data) {
                return '<div class="d-flex"><img class="ht-30 img-transaction" src="' . asset('/assets/images/users/avatar-1.jpg') . '" style="max-width:50px;"><br><div class="transaction-user"><h5>' . $data->user->userDetail->name . '</h5><span>' . $data->user->userDetail->second_name . '(' . $data->user->username . ')</span></div></div>';
            })

            ->addColumn('view_receipt', function ($pendingOrders) {
                if ($pendingOrders->paymentMethod->slug == 'bank-transfer')
                {
                    return '<a href="" data-bs-toggle="modal"
                        data-bs-target="#viewReceipt"    onclick="getReceipt(' . $pendingOrders->id . ')"
                        ><i class="fa fa-eye"></i></a>';
                }
            })

            ->rawColumns(['member', 'checkall', 'view_receipt', 'payment_method'])
            ->make(true);
    }

    public function getOrderReceipt($id)
    {
        $orderReceipt = CartPaymentReceipt::where('order_id', $id)->first();

        $view = view('order.receipt-view', compact('orderReceipt'));

        return response()->json([
            'status' => true,
            'data' => $view->render(),
        ], 200);
    }

    public function approveOrders(OrderApprovalRequest $request)
    {
        try {
            $orders = Order::whereIn('id', [...$request->order])->with('user.userDetail', 'orderDetails.package')->get();
            $userApproveService = new UserApproveService;
            $moduleStatus = $this->moduleStatus();
            $commissionService = new commissionService;
            $prefix = session()->get('prefix');
            foreach ($orders as $order) {
                DB::beginTransaction();

                $user = $order->user;
                $totalProductPrice = $totalProductPv = 0;
                $productId = null;
                foreach ($order->orderDetails as $key => $detail) {
                    $totalProductPv     += $detail->package->pair_value * $detail->quantity;
                    $totalProductPrice  += $detail->package->price * $detail->quantity;
                    $productId          = $detail->package->id;
                }
                $user->personal_pv = $user->personal_pv + $totalProductPv;
                $user->push();
                $ancestors = Treepath::where('descendant',$user->id)->pluck('ancestor');
                $userApproveService->insertPVhistoryDetailsNew($ancestors, $totalProductPv, 'personal_pv', $user->id, 'repurchase');
                $userApproveService->updateGroupPV($user, $totalProductPv, $user->id, 'repurchase');
                DB::commit();
                $commission = $this->runCalculation($user, $user->sponsor, $order->id, $productId, $totalProductPv, $totalProductPrice,'repurchase', $prefix);
                if ($commission) {
                    if ($moduleStatus->rank_status) {
                        $commissionService->updateUplineRank($user->id, $prefix);
                    }

                    $order->order_status = '1';
                    $order->orderDetails->order_status = '1';
                    $order->push();
                } else {
                    return redirect()->back()->withErrors('commission failed');
                }
            }

            return back()->with('success', 'Order approved successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
    }

    public function addPaymentReceipt(AddCartBankPaymentReceiptRequest $request)
    {
        DB::beginTransaction();
        try {
            $cartPaymentReceipt = new CartPaymentReceipt;
            $cartPaymentReceipt->user_id = Auth::user()->id;
            if ($request->has('reciept')) {
                $file = $request->file('reciept');
                $model = $cartPaymentReceipt;
                $prefix = 'cart';
                $folder = 'paymentReceipt';
                if (!$this->singleFileUpload($file, $model, $prefix, $folder)) {
                    DB::rollback();
                    if ($request->ajax()) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Payment receipt upload failed.',
                        ], 400);
                    }

                    return back()->with('error', 'Payment receipt upload failed.');
                }
                DB::commit();

                return response()->json(['success' => 'Receipt added successfully.']);
            }
        } catch (\Throwable $e) {
            DB::rollback();
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order Detail Submitting Error Occured!, please try again.',
                ], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function runCalculation($user, $sponsorData, $orderId, $productId, $totalProductPv, $totalProductPrice, $action = 'repurchase', $prefix)
    {
        $commission = new commissionService();
        $coreController = new CoreInfController();

        $compensation = $coreController->compensation();
        $error = false;
        $levelCommission = $compensation->sponsor_commission;
        $salesLevelCommission = $compensation->sales_Commission;

        if (!$commission->planCommission($user, $sponsorData, $orderId, $productId, $totalProductPv, $totalProductPrice, $prefix, $action)) {
            $error = 'error_in_plan_commission';
        }
        if ($levelCommission) {
            if ($salesLevelCommission) {
                $level =  $commission->SalesCommission($user, $sponsorData, $orderId, $productId,$totalProductPv, $totalProductPrice, $prefix, $action);
            } else {
                $level = $commission->levelCommission($user, $sponsorData, $orderId, $productId,$totalProductPv, $totalProductPrice, $prefix,'repurchase');
            }
            if (!$level)
                $error = 'error_in_level_commission';
        }
        if (!$commission->performanceBonus($user, $prefix)) {
            $error = 'error_in_performance_bonus';
        }
        if (!$error) {
            return true;
        }
        return false;
    }
}
