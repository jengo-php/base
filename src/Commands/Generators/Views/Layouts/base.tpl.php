<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><@echo $title ?? "Jengo Base App" ?></title>

    <@echo view('layouts/partials/header.layout.partial.php') ?>

    <@echo $this->renderSection('header') ?>
</head>

<body>
    <@echo $this->renderSection('content') ?>

    <@echo view('layouts/partials/footer.layout.partial.php') ?>

    <@echo $this->renderSection('footer') ?>
</body>
</html>