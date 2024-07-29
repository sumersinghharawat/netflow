<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Letterconfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LetterConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('letterconfigs')->delete();

        $lang = Language::select('code','id')->get();
        $data = [];

        foreach ($lang as $k => $ln) {
            if($ln['code'] == 'en'){
                $data[$k] =
                    [
                        'company_name' => 'Your Company Name',
                        'company_address' => 'REC,NIT,calicut,kerala',
                        'content' => '<p>Dear Distributor, Congratulations on your decision...! A journey of thousand miles must begin with a single step. I&#39;d like to welcome you to :company. We are excited that you have accepted our
                        business offer and agreed upon your start date. I trusted that this letter finds you mutually excited about your new opportunity with :company. Each of us will play a role to ensure your successful integration into the company.
                        Your agenda will involve planning your orientation with company and setting some initial work goals so that you feel immediately productive in your new role. And furthur growing into an integral part of this business.
                        Please note we are providing you an opportunity to earn money which is optional, your earnings will depend directly in the amount of efforts you put to develop your business. Again, welcome to the team. If you have questions prior to your start date,
                        please call me at any time, or send email if that is more convenient. We look forward to having you come onboard. The secret of success is constancy to purpose.asdas ALL THE BEST, SEE YOU AT TOP!!</p>',
                        'logo' => 'logo_default.png',
                        'place' => 'Calicut',
                        'language_id' => $ln['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
            } elseif($ln['code'] == 'es') {
                $data[$k] =
                    [
                        'company_name' => 'Your Company Name',
                        'company_address' => 'REC,NIT,calicut,kerala',
                        'content' => '<p> Estimado Distribuidor,
                            ¡Felicitaciones por tu decisión ...!
                            Un viaje de mil millas debe comenzar con un solo paso.
                            Me gustaría darte la bienvenida a :company. Nos complace que haya aceptado nuestra oferta comercial y acordado su fecha de inicio. Confié en que esta carta lo encuentre mutuamente entusiasmado con su nueva oportunidad con :company. Cada uno de nosotros desempeñará un papel para garantizar su integración exitosa en la empresa. Su agenda implicará planificar su orientación con la empresa y establecer algunos objetivos de trabajo iniciales para que se sienta productivo de inmediato en su nuevo cargo. Y, además, convertirse en una parte integral de este negocio. Tenga en cuenta que le brindamos la oportunidad de ganar dinero, que es opcional, sus ganancias dependerán directamente de la cantidad de esfuerzos que realice para desarrollar su negocio.
                            De nuevo, bienvenido al equipo. Si tiene preguntas antes de su fecha de inicio, llámeme en cualquier momento o envíe un correo electrónico si es más conveniente. Esperamos contar con usted a bordo.
                            El secreto del éxito es la constancia en el propósito.
                            TODO LO MEJOR, ¡TE VOCIENDO EN LA PARTE SUPERIOR!',
                        'logo' => 'logo_86777159.png',
                        'place' => 'Calicut',
                        'language_id' => $ln['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
            } elseif($ln['code'] == 'ch') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC，NIT，卡利卡特，喀拉拉邦',
                        'content'                =>      '<p>尊敬的经销商，
                                                            祝贺您的决定...！
                                                        一千英里的旅程必须从第一步开始。
                                                            我想欢迎您加入COMPANY NAME。很高兴您接受我们的业务报价并同意您的开始日期。我相信，这封信会让您为与“公司名称”带来的新机遇而感到兴奋。我们每个人都将扮演确保您成功融入公司的角色。您的议程将包括计划在公司的入职培训并设定一些初始工作目标，以使您在担任新职务时立即感到富有成效。并且进一步发展成为该业务不可或缺的一部分。请注意，我们为您提供了赚钱的机会，这是可选的，您的收入将直接取决于您为发展业务而付出的努力。
                                                            再次欢迎您加入团队。如果您在开始日期之前有任何疑问，请随时致电我，或者如果方便的话可以发送电子邮件。我们期待您的加入。
                                                        成功的秘诀在于坚持目标。
                                                        祝一切顺利，见到您！</ p>',
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>        $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            } elseif($ln['code'] == 'de') {
                $data[$k] =
                    [
                        'company_name' => 'Your Company Name',
                        'company_address' => 'REC,NIT,calicut,kerala',
                        'content' => '<p> Lieber Distributor,
                                                                Herzlichen Glückwunsch zu Ihrer Entscheidung ...!
                                                                Eine Reise von tausend Meilen muss mit einem einzigen Schritt beginnen.
                                                                Ich möchte Sie bei FIRMENNAME begrüßen. Wir freuen uns, dass Sie unser Geschäftsangebot angenommen und Ihren Starttermin vereinbart haben. Ich vertraute darauf, dass Sie sich in diesem Brief gegenseitig über Ihre neue Chance mit FIRMENNAME freuen. Jeder von uns wird eine Rolle spielen, um Ihre erfolgreiche Integration in das Unternehmen zu gewährleisten. Ihre Agenda beinhaltet die Planung Ihrer Ausrichtung mit dem Unternehmen und die Festlegung einiger anfänglicher Arbeitsziele, damit Sie sich in Ihrer neuen Rolle sofort produktiv fühlen. Und weiter wachsen zu einem integralen Bestandteil dieses Geschäfts. Bitte beachten Sie, dass wir Ihnen die Möglichkeit bieten, optionales Geld zu verdienen. Ihre Einnahmen hängen direkt von den Anstrengungen ab, die Sie zur Entwicklung Ihres Geschäfts unternommen haben.
                                                                Nochmals herzlich willkommen im Team. Wenn Sie Fragen vor Ihrem Starttermin haben, rufen Sie mich bitte jederzeit an oder senden Sie eine E-Mail, wenn dies praktischer ist. Wir freuen uns, Sie an Bord zu haben.
                                                                Das Erfolgsgeheimnis ist Konstanz zum Zweck
                                                                Alles Gute, wir sehen uns oben !!
                                                                </ p>',
                        'logo' => 'logo_86777159.png',
                        'place' => 'Calicut',
                        'language_id' => $ln['id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
            } elseif($ln['code'] == 'pt') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC,NIT,calicut,kerala',
                        'content'                =>      '<p>Prezado Distribuidor,
                                                            Parabéns pela sua decisão ...!
                                                            Uma jornada de mil milhas deve começar com um único passo.
                                                            Gostaria de recebê-lo no nome da empresa. Estamos empolgados por você ter aceitado nossa oferta comercial e ter concordado com sua data de início. Confiei que esta carta o deixasse empolgado com sua nova oportunidade com a NOME DA EMPRESA. Cada um de nós desempenhará um papel para garantir sua integração bem-sucedida na empresa. Sua agenda envolverá o planejamento de sua orientação com a empresa e o estabelecimento de algumas metas de trabalho iniciais, para que você se sinta imediatamente produtivo em sua nova função. E a furthur está se tornando parte integrante desse negócio. Observe que estamos oferecendo a você uma oportunidade de ganhar dinheiro opcional, seus ganhos dependerão diretamente da quantidade de esforços que você fizer para desenvolver seus negócios.
                                                            Mais uma vez, bem-vindo à equipe. Se você tiver dúvidas antes da data de início, ligue para mim a qualquer momento ou envie um e-mail, se for mais conveniente. Esperamos ter você a bordo.
                                                            O segredo do sucesso é a constância para o propósito.
                                                            TODO O MELHOR, Vejo você no topo !!
                                                            </p>',
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>        $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            } elseif($ln['code'] == 'fr') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC,NIT,calicut,kerala',
                        'content'                =>      "<p> Cher distributeur,
                                                            Félicitations pour votre décision ...!
                                                            Un voyage de mille kilomètres doit commencer par un seul pas.
                                                            Je souhaite vous souhaiter la bienvenue à COMPANY NAME. Nous sommes ravis que vous ayez accepté notre offre commerciale et convenu de votre date de début. J'espère que cette lettre vous trouvera mutuellement enthousiasmé par votre nouvelle opportunité avec COMPANY NAME. Chacun de nous jouera un rôle pour assurer votre intégration réussie dans la société. Votre ordre du jour impliquera de planifier votre orientation avec l’entreprise et de fixer des objectifs de travail initiaux afin que vous vous sentiez immédiatement productif dans votre nouveau rôle. Et furthur devenant une partie intégrante de cette entreprise. Veuillez noter que nous vous offrons la possibilité de gagner de l'argent, ce qui est facultatif. Vos revenus dépendront directement des efforts que vous déploierez pour développer votre entreprise.
                                                            Encore une fois, bienvenue dans l'équipe. Si vous avez des questions avant votre date de début, appelez-moi à tout moment ou envoyez un e-mail si cela vous convient mieux. Nous sommes impatients de vous voir à bord.
                                                            Le secret du succès est la constance à purpose.asdas
                                                            TOUS LES MEILLEURS, Rendez-vous au sommet! </ P>",
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>      $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            } elseif($ln['code'] == 'it') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC,NIT,calicut,kerala',
                        'content'                =>      "<p> Gentile distributore,
                                                            Congratulazioni per la tua decisione ...!
                                                            Un viaggio di mille miglia deve iniziare con un solo passo.
                                                            Mi piacerebbe darti il ​​benvenuto in NOME AZIENDA. Siamo lieti che tu abbia accettato la nostra offerta commerciale e concordato la tua data di inizio. Mi fidavo che questa lettera ti trovasse reciprocamente entusiasta della tua nuova opportunità con COMPANY NAME. Ognuno di noi svolgerà un ruolo per garantire la corretta integrazione nella società. La tua agenda coinvolgerà la pianificazione del tuo orientamento con l'azienda e la definizione di alcuni obiettivi di lavoro iniziali in modo da sentirti immediatamente produttivo nel tuo nuovo ruolo. E Furthur sta diventando parte integrante di questo business. Ti preghiamo di notare che ti stiamo offrendo l'opportunità di guadagnare denaro che è facoltativo, i tuoi guadagni dipenderanno direttamente dalla quantità di sforzi che fai per sviluppare la tua attività.
                                                            Ancora una volta, benvenuto nella squadra. Se hai domande prima della data di inizio, chiamami in qualsiasi momento o invia un'e-mail se è più conveniente. Non vediamo l'ora di farti salire a bordo.
                                                            Il segreto del successo è la costanza allo scopo.asdas
                                                            TUTTO IL MEGLIO, CI VEDIAMO AL TOP !! </p>",
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>      $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            } elseif($ln['code'] == 'tr') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC,NIT,calicut,kerala',
                        'content'                =>      "<p> Sayın Bayimiz,
                                                            Kararın için tebrikler ...!
                                                            Bin mil yolculuk tek bir adımla başlamalıdır.
                                                            ŞİRKET ADI 'na hoş geldiniz demek istiyorum. İş teklifimizi kabul ettiğiniz ve başlangıç ​​tarihinizi kabul ettiğiniz için çok heyecanlıyız. Bu mektubun, COMPANY NAME ile yeni fırsatınız için karşılıklı olarak sizi heyecanlandıracağına inandım. Her birimiz şirkete başarılı bir şekilde entegrasyonunuzu sağlamada rol oynayacağız. Gündeminiz, şirketinize yöneliminizi planlamayı ve yeni görevinizde derhal üretken hissetmenizi sağlayacak bazı başlangıç ​​çalışma hedefleri belirlemeyi içerecektir. Ve furthur bu işin ayrılmaz bir parçası haline geliyor. Lütfen, isteğe bağlı olarak para kazanma fırsatı sunduğumuzu unutmayın, kazancınız doğrudan işinizi geliştirmek için harcadığınız çaba miktarına bağlı olacaktır.
                                                            Yine takıma hoş geldin. Başlama tarihinden önce sorularınız varsa, lütfen beni istediğiniz zaman arayın veya daha uygunsa e-posta gönderin. Gemiye gelmeni dört gözle bekliyoruz.
                                                            Başarının sırrı amaç için tutarlılıktır.
                                                            TÜM EN İYİ, TOP SİZE GÖRMEK !!</p>",
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>      $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            } elseif($ln['code'] == 'po') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC,NIT,calicut,kerala',
                        'content'                =>     "<p> Drogi dystrybutorze,
                                                            Gratulujemy twojej decyzji ...!
                                                            Podróż tysiąca mil musi rozpocząć się od jednego kroku.
                                                            Chciałbym powitać Cię w NAZWIE FIRMY. Cieszymy się, że zaakceptowałeś naszą ofertę biznesową i ustaliłeś datę rozpoczęcia. Ufałem, że ten list wzbudza wzajemne podekscytowanie nowymi możliwościami w COMPANY NAME. Każdy z nas odegra pewną rolę w zapewnieniu pomyślnej integracji z firmą. Twój plan będzie obejmował planowanie orientacji w firmie i ustalenie początkowych celów pracy, abyś od razu poczuł się produktywny w nowej roli. I stając się integralną częścią tego biznesu. Pamiętaj, że zapewniamy Ci możliwość zarobienia pieniędzy, która jest opcjonalna, Twoje zarobki będą zależeć bezpośrednio od nakładów włożonych w rozwój Twojej firmy.
                                                            Ponownie witamy w zespole. Jeśli masz pytania przed datą rozpoczęcia, zadzwoń do mnie w dowolnym momencie lub wyślij e-mail, jeśli jest to wygodniejsze. Z niecierpliwością czekamy na Ciebie.
                                                            Sekret sukcesu tkwi w stałości celu. Asdas
                                                            WSZYSTKO NAJLEPSZE, DO ZOBACZENIA NA GÓRĘ !!</p>",
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>      $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            } elseif($ln['code'] == 'ar') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC ، NIT ، كاليكت ، ولاية كيرالا',
                        'content'                =>     "<p> عزيزي الموزع ،
                                                            مبروك على قرارك ...!
                                                            يجب أن تبدأ رحلة الألف ميل بخطوة واحدة.
                                                            أرغب في الترحيب بكم في اسم الشركة. نحن متحمسون لأنك قبلت عرض أعمالنا ووافقت على تاريخ البدء. لقد وثقت في أن هذه الرسالة تجدك متحمسًا بشأن فرصتك الجديدة مع اسم الشركة. سوف يلعب كل منا دورًا لضمان اندماجك الناجح في الشركة. سوف يتضمن جدول أعمالك تخطيط توجهك مع الشركة وتحديد بعض أهداف العمل الأولية بحيث تشعر على الفور بالإنتاجية في دورك الجديد. وزيادة فورثور إلى جزء لا يتجزأ من هذا العمل. يرجى ملاحظة أننا نوفر لك فرصة لكسب المال وهو أمر اختياري ، وسوف تعتمد أرباحك مباشرة في مقدار الجهود التي تبذلها لتطوير عملك.
                                                            مرة أخرى ، مرحبا بكم في الفريق. إذا كانت لديك أسئلة قبل تاريخ البدء ، فيرجى الاتصال بي في أي وقت ، أو إرسال بريد إلكتروني إذا كان ذلك أكثر ملاءمة. ونحن نتطلع إلى أن تأتي على متن الطائرة.
                                                            سر النجاح هو الثبات على الغرض.
                                                            كل التوفيق ، أراك في الأعلى !! </ p>",
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>      $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            } elseif($ln['code'] == 'ru') {
                $data[$k] =
                    [
                        'company_name'           =>     'Your Company Name',
                        'company_address'        =>      'REC,NIT,calicut,kerala',
                        'content'                =>      "<p> Уважаемый дистрибьютор!
                                                            Поздравляю с решением ...!
                                                            Путь в тысячу миль должен начинаться с одного шага.
                                                            Я хотел бы приветствовать вас в НАИМЕНОВАНИИ КОМПАНИИ. Мы рады, что вы приняли наше деловое предложение и согласовали дату начала. Я полагал, что это письмо находит вас взаимно взволнованными по поводу вашей новой возможности с ИМЯ КОМПАНИИ. Каждый из нас сыграет свою роль в обеспечении вашей успешной интеграции в компанию. Ваша повестка дня будет включать планирование вашей ориентации в компании и установление некоторых начальных рабочих целей, чтобы вы сразу почувствовали себя продуктивными в своей новой роли. И дальнейшее превращение в неотъемлемую часть этого бизнеса. Обратите внимание, что мы предоставляем вам возможность зарабатывать деньги, что является необязательным, ваш заработок будет напрямую зависеть от того, сколько усилий вы приложите для развития своего бизнеса.
                                                            Еще раз добро пожаловать в команду. Если у вас есть вопросы до даты начала, пожалуйста, позвоните мне в любое время или отправьте электронное письмо, если это более удобно. Мы с нетерпением ждем, чтобы вы пришли на борт.
                                                            Секрет успеха в постоянстве цели. Asdas
                                                            ВСЕХ ЛУЧШИХ, Увидимся на вершине !! </ p>",
                        'logo'                   =>      'logo_86777159.png',
                        'place'                  =>      'Calicut',
                        'language_id'            =>      $ln['id'],
                        'created_at'             =>      now(),
                        'updated_at'             =>      now(),
                    ];
            }
        }
        Letterconfig::insert($data);
    }
}
