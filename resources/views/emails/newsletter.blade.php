<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #1a1a1a;
        }
        .header img {
            max-width: 200px;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 20px;
            border-top: 1px solid #eee;
        }
        a {
            color: #e51717;
        }
        .btn {
            display: inline-block;
            background-color: #e51717;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/images/logo_embaixada.png') }}" alt="Embaixada Discos">
    </div>
    
    <div class="content">
        {!! $content !!}
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Embaixada Discos. Todos os direitos reservados.</p>
        <p>
            Você recebeu esse e-mail porque se inscreveu em nossa lista de newsletter.
            <a href="{{ url('/newsletter/unsubscribe?email=' . urlencode($to[0]['address'] ?? '')) }}">Cancelar inscrição</a>
        </p>
    </div>
</body>
</html>
