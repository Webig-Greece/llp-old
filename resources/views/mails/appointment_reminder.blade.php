<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointment Reminder</title>
</head>

<body>
    <h1>Appointment Reminder</h1>
    <p>Dear {{ $patientName }},</p>
    <p>This is a reminder for your appointment with {{ $practitionerName }} on {{ $appointmentDate }}.</p>
    <p>Notes: {{ $notes }}</p>
    <p>Thank you,</p>
    <p>LifeLiftPro Team</p>
</body>

</html>
