<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\ReplicaContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReplicaContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('replica_contents')->delete();

        $language = Language::where('code', 'en')->orWhere('default', 1)->first();
        //TODO img src in value
        $data = [
            [
                'key' => 'home_title1',
                'value' => 'software name v1.1',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'home_title2',
                'value' => 'software title and some heading content',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'plan',
                'value' => '<div class="row">
				<div class="col-xl-3 col-lg-4 col-md-6">
					<div class="services__one-item">
					
						<div class="services__one-item-content">
							<div class="services__one-item-content-icon">
								<i class="flaticon-family"></i>
							</div>
							<h4><a href="#">Plan header 1</a></h4>
							<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.</p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6">
					<div class="services__one-item active">
					
						<div class="services__one-item-content">
							<div class="services__one-item-content-icon">
								<i class="flaticon-car-insurance"></i>
							</div>
							<h4><a href="#">Plan header 2</a></h4>
							<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.</p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6">
					<div class="services__one-item">
					
						<div class="services__one-item-content">
							<div class="services__one-item-content-icon">
								<i class="flaticon-healthcare"></i>
							</div>
							<h4><a href="#">Plan header 3</a></h4>
							<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.</p>
						</div>
					</div>
				</div>
				<div class="col-xl-3 col-lg-4 col-md-6">
					<div class="services__one-item">
					
						<div class="services__one-item-content">
							<div class="services__one-item-content-icon">
								<i class="flaticon-home-insurance"></i>
							</div>
							<h4><a href="#">Plan header 4</a></h4>
							<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans.</p>
						</div>
					</div>
				</div>
			</div>',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone',
                'value' => '999999999',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_mail',
                'value' => 'companyname@mail.in',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_address',
                'value' => 'address',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'policy',
                'value' => '<p>All subscribers of MLM services agree to be bound by the terms of this service. The MLM software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more</p>
                ',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'terms',
                'value' => '<p>All subscribers of MLM services agree to be bound by the terms of this service. The MLM software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more</p>',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'about',
                'value' => '<div class="container">
                <div class="row align-items-center">
                    <div class="col-xxl-7 col-lg-6 lg-mb-30">
                        <div class="about__two-left">
                            <div class="about__two-left-image">
                                <div class="image-overlay dark__image">
                                    <img src="' . env('APP_URL') . 'assets/replica/img/about/about-1.jpg" alt="about-image">
                                </div>
                                <div class="about__two-left-image-two image-overlay dark__image">
                                    <img src="' . env('APP_URL') . 'assets/replica/img/about/about-2.jpg" alt="about-image">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-5 col-lg-6">
                        <div class="about__two-right">
                            <div class="about__two-right-title">
                                <h2><b>About Us</b></h2>
                                <p>Company title and some description about title and some description about title and some.</p>
                            </div><p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company Company name. More over these we are keen to construct MLM software as per the business plan suggested by the clients.<br>This MLM software is featured of with integrated with SMS, E-Wallet, Replicating Website, E-Pin, E-Commerce Shopping Cart,Web Design.</p>
    
    
                            <!--<div class="about__two-right-bottom">
                                <div class="about__two-right-bottom-list">
                                    <div class="about__two-right-bottom-list-item mb-25">
                                        
                                        <div class="about__two-right-bottom-list-item-content">
                                            <h5>Lorem Ipsum</h5>
                                            <p>Lorem Ipsum is simply dummy text.</p>
                                        </div>
                                    </div>
                                    <div class="about__two-right-bottom-list-item">
                                        
                                        <div class="about__two-right-bottom-list-item-content">
                                            <h5>Lorem Ipsum</h5>
                                            <p>Lorem Ipsum is simply dummy text.</p>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>-->
                </div>
            </div></div></div>',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'why_choose_us',
                'value' => '<div class="row align-items-center">
				<div class="col-xxl-7 col-xl-6 col-lg-12 xl-mb-20">
					<div class="benefits__area-left">
						<div class="benefits__area-left-image">
							<div class="image-overlay dark__image">
								<img src="' . env('APP_URL') . 'assets/replica/img/pages/benefits.jpg" alt="benefits-image">
							</div>
						
						</div>
					</div>
				</div>
				<div class="col-xxl-5 col-xl-6 col-lg-12">
					<div class="benefits__area-right">
						<div class="benefits__area-right-title">
							<h2><b>Why Choose Us</b></h2>
							<p>Our track record speaks for itself. We have built a strong reputation for reliability, integrity, and exceptional service within the industry.
							</p>
						</div>
						<div class="benefits__area-right-list">
							<p><i class="flaticon-check-mark"></i>we have the knowledge and expertise to deliver exceptional results.</p>
							<p><i class="flaticon-check-mark"></i> Our track record speaks for itself. We have built a strong reputation for reliability.</p>
							<p><i class="flaticon-check-mark"></i>

We understand the importance of deadlines. You can rely on us to deliver your product or service on time, every time.</p>
							<p><i class="flaticon-check-mark"></i>

We are constantly evolving and adapting to the changing landscape of our industry.</p>
						</div>
						
					</div>
				</div>
			</div>',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'features',
                'value' => '<div class="row">
                <div class="col-xl-12">
                    <div class="features-area">
                        <div class="features-area-item">
							<div class="features-area-item-icon">
								<i class="flaticon-trust"></i>
							</div>
							<div class="features-area-item-content">
								<h4>Feature 1</h4>
								<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. </p>
							</div>
                        </div>
                        <div class="features-area-item features-area-item-hover">
							<div class="features-area-item-icon">
								<i class="flaticon-umbrella-1"></i>
							</div>
							<div class="features-area-item-content">
								<h4>Feature 2</h4>
								<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. </p>
							</div>
                        </div>
                        <div class="features-area-item">
							<div class="features-area-item-icon">
								<i class="flaticon-saving"></i>
							</div>
							<div class="features-area-item-content">
								<h4>Feature 3</h4>
								<p>The software is an entire solution for all type of business plan like Binary,Matrix, Unilevel and many other compensation plans. </p>
							</div>
                        </div>
                    </div>
                </div>
            </div>',
                'lang_id' => $language->id,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];
        ReplicaContent::insert($data);  
    }
}
