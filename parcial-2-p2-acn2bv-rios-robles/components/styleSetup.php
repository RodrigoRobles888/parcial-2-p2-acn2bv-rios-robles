<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
$tema = $_GET['tema'] ?? 'claro';
if ($tema === 'oscuro') {
    $bgColor = '#292929ff';
    $textColor = '#eee';
} else {
    $bgColor = '#f5f5f5';
    $textColor = '#000';
}
?>
<style>
    body {
        background-color: <?= $bgColor ?>;
        color: <?= $textColor ?>;
    }

    header {
        background-color: rgba(20, 20, 20, 1);
        color: white;
        text-align: center;
        font-size: 20px;
        padding: 15px;
    }

    header nav a {
        color: white;
        margin: 10px;
        font-weight: 600;
        text-decoration: none;
    }

    header nav a:hover {
        background-color: rgba(0, 0, 0, 0.1);
        transform: scale(1.2);
    }

    .tituloppal,
    .descripcionpag {
        text-align: center;
    }

    .container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .card {
        margin: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .formulario {
        padding: 5%;
        background-color: rgba(0, 0, 0, 0.1);
        margin: 4%;
        border-radius: 10px;
    }

    footer {
        background-color: rgba(20, 20, 20, 1);
        color: white;
        text-align: center;
        padding: 15px;
        font-size: 18px;
    }
</style>