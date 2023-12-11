<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/style.css">
    <title><?= $data['title']; ?></title>
</head>

<body>

    <h3><u><?= $data['title']; ?></u></h3>

    <p>
        <a href="/Instructeur/overzichtInstructeur">Ga terug</a>
    </p>

    <?php if ($data['message']) : ?>
        <p id="message" style="color:red"><?= $data['message'] ?></p>
        <script>
            setTimeout(() => document.getElementById("message").remove(), 3000);
        </script>
    <?php endif ?>

    <table>
        <thead>
            <th>TypeVoertuig</th>
            <th>Type</th>
            <th>Kenteken</th>
            <th>Bouwjaar</th>
            <th>Brandstof</th>
            <th>RijbewijsCategorie</th>
            <th>Instructeur</th>
            <th>Wijzigen</th>
            <th>Verwijderen</th>
        </thead>
        <tbody>
            <?= $data['tableRows']; ?>
        </tbody>
    </table>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>