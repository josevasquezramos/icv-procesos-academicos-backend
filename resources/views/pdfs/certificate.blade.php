<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Certificado de {{ $courseName }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        @font-face {
            font-family: 'Arial';
            font-style: normal;
            font-weight: normal;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }
        
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.4;
        }
        
        .page {
            position: relative;
            width: 100%;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            page-break-inside: avoid;
        }
        
        .first-page {
            page-break-after: always;
        }
        
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            object-fit: cover;
        }
        
        .diploma-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            text-align: center;
            z-index: 1;
        }
        
        .detail-content {
            padding: 40px 50px;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }
        
        .logo {
            width: 300px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #1a4b8c;
            font-size: 28px;
            margin: 5px 0;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        
        h2 {
            color: #2c6ab1;
            font-size: 26px;
            margin: 5px 0;
        }
        
        .student-name {
            font-size: 28px;
            font-weight: bold;
            color: #1a4b8c;
            margin: 20px auto;
            padding: 5px 0;
            border-top: 3px solid #2c6ab1;
            border-bottom: 3px solid #2c6ab1;
            width: 85%;
            max-width: 650px;
        }
        
        .course-name {
            font-size: 24px;
            font-weight: bold;
            color: black;
            margin: 20px 0;
        }
        
        .dates {
            margin: 5px 0;
            color: #555;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .qr-code {
            margin-top: 20px;
        }
        
        .qr-code img {
            width: 130px;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        table {
            width: 90%;
            border-collapse: collapse;
            margin: 5px 0;
            background-color: white;
            border: 1px solid #ddd;
            table-layout: fixed;
        }
        
        th {
            background-color: #2c6ab1;
            color: white;
            padding: 2px 5px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 2px 5px;
            border-bottom: 1px solid #e0e0e0;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        tr:nth-child(even) {
            background-color: #f8fbff;
        }
        
        .verification {
            margin-top: 20px;
            padding: 5px 8px;
            background-color: #f0f7ff;
            border-left: 5px solid #2c6ab1;
            width: 88%;
        }
        
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
            padding-top: 5px;
            border-top: 1px solid #e0e0e0;
        }
        
        p {
            margin: 4px 0;
            font-size: 16px;
        }
        
        strong {
            color: #2c6ab1;
        }
    </style>
</head>

<body>
    <!-- Página 1: Diploma con fondo -->
    <div class="page first-page">
        <img src="{{ public_path('images/certificate_background.png') }}" class="background-image" alt="Fondo certificado">
        
        <div class="diploma-content">
            <img src="{{ $logoImage }}" alt="Logo" class="logo">

            <h1>Certificado de Finalización</h1>

            <p style="font-size: 18px;">Se otorga el presente certificado a:</p>

            <div class="student-name">{{ $studentName }}</div>

            <p style="font-size: 18px;">Por haber completado exitosamente el curso:</p>

            <div class="course-name">{{ $courseName }}</div>

            <div class="dates">
                <strong>Finalizado el:</strong> {{ $completionDate }}<br>
                <strong>Emitido el:</strong> {{ $issueDate }}
            </div>

            <div class="qr-code">
                <img src="{{ $qrCodeImage }}" alt="Código QR de verificación">
            </div>
        </div>
    </div>

    <!-- Página 2: Detalles Académicos sin fondo -->
    <div class="page">
        <div class="detail-content">
            <h1>Detalles Académicos</h1>

            <div class="info-section">
                <p><strong>Curso:</strong> {{ $courseName }}</p>
                <p><strong>Estudiante:</strong> {{ $studentName }}</p>
                <p><strong>DNI:</strong> {{ $dni }}</p>
            </div>

            <div class="info-section">
                <h2>Información General</h2>
                <p><strong>Nota Final:</strong> {{ number_format($academicData['final_grade'], 2) }}/{{ $baseGrade }}</p>
                <p><strong>Estado:</strong> {{ $academicData['program_status'] }}</p>
                <p><strong>Escala:</strong> 0 - {{ $baseGrade }} | <strong>Aprobación:</strong> {{ $minPassingGrade }}</p>
            </div>

            <div class="info-section">
                <h2>Evaluaciones</h2>
                @if(count($academicData['evaluations']) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Evaluación</th>
                            <th>Tipo</th>
                            <th>Peso</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academicData['evaluations'] as $evaluation)
                        <tr>
                            <td>{{ $evaluation['title'] }}</td>
                            <td>{{ $evaluation['type'] }}</td>
                            <td>{{ $evaluation['weight'] }}%</td>
                            <td>{{ number_format($evaluation['obtained_grade'], 2) }}/{{ $baseGrade }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>No hay evaluaciones registradas</p>
                @endif
            </div>

            <div class="verification">
                <p style="word-break: break-all; font-size: 14px;">{{ $verificationUrl }}</p>
            </div>
        </div>
    </div>
</body>

</html>