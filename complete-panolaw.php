<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>신청 완료</title>

    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '2479565725812430');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=2479565725812430&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Pretendard', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f8f9fa;
        }
        .complete-box {
            text-align: center;
            padding: 40px;
        }
        .complete-box h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 12px;
        }
        .complete-box p {
            font-size: 15px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="complete-box">
        <h2>상담 신청이 완료되었습니다.</h2>
        <p>빠른 시간 내에 연락드리겠습니다.</p>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 1000);
    </script>
</body>
</html>
