<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mailData['subject'] }}</title>
    <style>
        @media (max-width:580px){
                .mob_full{width: 100% !important;}
                .summery-section{padding:5px !important;}
                .heading-section{padding:5px !important;}
        }
    </style>
</head>
<body>
   <section>
    <div class="mailer-section" style="width: 100%;max-width: 100%; background-color: #E8E8E8; text-align: center;font-family: sans-serif; margin: auto;"  >
        <div class="content-section" style="width: 82%;max-width: 82%;background-color: #FFFFFF; padding: 15px;margin: auto; display: inline-block;  margin-top: 30px; display: inline-block;">
        <div class="logo-section" style="padding-left: 25px; padding-top: 10px;">
            <img src="{{ asset('assets/images/'.$mailData['logo']) }}" style="padding-top: 10px; float: left;width:30%; hight:auto;">
       </div>

       <div class="summery-section" style="float: left; font-size: 16px; text-align: left; padding-left: 25px;">
        {!! $mailData['content'] !!}
       </div>

    </div>

       <div class="footer-section" style="padding: 10px;">
        <div class="footer-summery">
            <p style="line-height: 30px; font-size: 12px;">
             {{-- KSITIL Special Economic Zone Unit No 03, 2nd floor, "Sahya" Govt Cyber Park, <br>
             Calicut - 673016, Kerala, India --}}
             {!! $mailData['footer'] !!}
            </p>

        </div>
        <div class="footer-icon-section" style="width: 100%;text-align: center;">


       </div>
       </div>
    </div>

   </section>
</body>
</html>
