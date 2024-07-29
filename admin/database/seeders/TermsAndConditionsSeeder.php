<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\TermsAndCondition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermsAndConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('terms_and_conditions')->delete();
        $lang = Language::whereIn('name_in_english', ['english', 'spanish', 'chinese', 'german', 'french'])->get();
        foreach ($lang as $key => $value) {
            if($value->name_in_english == 'english') {
                $data[$key]['terms_and_conditions'] = "All subscribers of SOFTWARE NAME services agree to be bound by the terms of this service. The SOFTWARE NAME software is an entire solution for all type of business plan like Binary, Matrix, Unilevel and many other compensation plans. This is developed by a leading MLM software development company COMPANY NAME. More over these we are keen to construct MLM software as per the business plan suggested by the clients.This MLM software is featured of with integrated with SMS, E-Wallet,Replicating Website,E-Pin,E-Commerce, Shopping Cart,Web Design and more.";
            } elseif ($value->name_in_english == 'spanish') {
                $data[$key]['terms_and_conditions'] = "Todos los suscriptores de los servicios de NOMBRE DEL SOFTWARE aceptan estar sujetos a los términos de este servicio. El software SOFTWARE NAME es una solución completa para todo tipo de plan de negocios como Binary, Matrix, Unilevel y muchos otros planes de compensación. Esto es desarrollado por una empresa líder en desarrollo de software MLM NOMBRE DE LA COMPAÑÍA. Además de estos, estamos interesados ​​​​en construir software MLM según el plan de negocios sugerido por los clientes. Diseño y más.";
            } elseif ($value->name_in_english == 'chinese') {
                $data[$key]['terms_and_conditions'] = "SOFTWARE NAME 服务的所有订户均同意受本服务条款的约束。 SOFTWARE NAME 软件是一个完整的解决方案，适用于所有类型的商业计划，如 Binary、Matrix、Unilevel 和许多其他薪酬计划。这是由领先的传销软件开发公司 COMPANY NAME 开发的。除此之外，我们热衷于根据客户建议的商业计划构建 MLM 软件。该 MLM 软件的特点是与 SMS、电子钱包、复制网站、E-Pin、电子商务、购物车、Web 集成设计等等。
                SOFTWARE NAME fúwù de suǒyǒu dìnghù jūn tóngyì shòu běn fúwù tiáokuǎn de yuēshù. SOFTWARE NAME ruǎnjiàn shì yīgè wánzhěng de jiějué fāng'àn, shìyòng yú suǒyǒu lèixíng de shāngyè jìhuà, rú Binary,Matrix,Unilevel hé xǔduō qítā xīnchóu jìhuà. Zhè shì yóu lǐngxiān de chuánxiāo ruǎnjiàn kāifā gōngsī COMPANY NAME kāifā de. Chú cǐ zhī wài, wǒmen rèzhōng yú gēnjù kèhù jiànyì de shāngyè jìhuà gòujiàn MLM ruǎnjiàn. Gāi MLM ruǎnjiàn de tèdiǎn shì yǔ SMS, diànzǐ qiánbāo, fùzhì wǎngzhàn,E-Pin, diànzǐ shāngwù, gòuwù chē,Web jíchéng shèjì děng děng.";
            } elseif ($value->name_in_english == 'german') {
                $data[$key]['terms_and_conditions'] = "Alle Abonnenten von SOFTWARENAME-Diensten stimmen zu, an die Bedingungen dieses Dienstes gebunden zu sein. Die Software SOFTWARE NAME ist eine Gesamtlösung für alle Arten von Geschäftsplänen wie Binary, Matrix, Unilevel und viele andere Vergütungspläne. Dies wird von einem führenden MLM-Softwareentwicklungsunternehmen NAME DES UNTERNEHMENS entwickelt. Darüber hinaus sind wir sehr daran interessiert, MLM-Software gemäß dem von den Kunden vorgeschlagenen Geschäftsplan zu erstellen. Diese MLM-Software ist mit integrierter SMS, E-Wallet, replizierender Website, E-Pin, E-Commerce, Einkaufswagen und Web ausgestattet Gestalten und mehr.";
            } elseif ($value->name_in_english == 'french') {
                $data[$key]['terms_and_conditions'] = "Tous les abonnés aux services SOFTWARE NAME acceptent d'être liés par les conditions de ce service. Le logiciel SOFTWARE NAME est une solution complète pour tout type de plan d'affaires comme Binary, Matrix, Unilevel et de nombreux autres plans de rémunération. Ceci est développé par une société leader de développement de logiciels MLM NOM DE LA SOCIÉTÉ. De plus, nous tenons à construire un logiciel MLM selon le plan d'affaires suggéré par les clients. Conception et plus encore.";
            }
            $data[$key]['language_id'] = $value->id;
            $data[$key]['created_at'] = now();
            $data[$key]['updated_at'] = now();
        }
        TermsAndCondition::insert($data);
    }
}
