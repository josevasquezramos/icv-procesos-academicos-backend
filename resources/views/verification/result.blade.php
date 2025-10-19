<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Certificado</title>
    <meta name="robots" content="noindex">

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: grid;
            place-items: center;
            min-height: 90vh;
            background-color: #f4f5f7;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 40px;
            text-align: center;
            max-width: 500px;
        }

        .valid {
            border-top: 5px solid #28a745;
        }

        .invalid {
            border-top: 5px solid #dc3545;
        }

        h1 {
            margin-top: 0;
        }

        h2 {
            margin: 0;
            font-size: 1.75rem;
        }

        p {
            color: #555;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="card {{ $credential ? 'valid' : 'invalid' }}">

        @if ($credential)
            <h1>Certificado Válido</h1>
            <p>Se confirma que el siguiente certificado es auténtico:</p>

            <h2>{{ $credential->user->full_name }}</h2>
            <p>
                Completó exitosamente el curso de:<br>
                <strong>{{ $credential->group->course->name }}</strong>
            </p>
            <p>
                Emitido el: {{ $credential->issue_date->format('d/m/Y') }}
            </p>
        @else
            <h1>Certificado No Válido</h1>
            <p>
                El código de verificación no es válido o no se ha encontrado
                en nuestros registros.
            </p>
        @endif

    </div>

</body>

</html>