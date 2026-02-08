<@echo $this->extend('layouts/{layout}.layout.php') ?>

<@echo $this->section('header'); ?>
   <@echo $this->renderSection('header'); ?>

   <!-- add runtime changes to the head element here -->
<@echo $this->endSection(); ?>

<@echo $this->section('content'); ?>
   <@echo $this->renderSection('content'); ?>

   <!-- Customize this content section as you would like -->
<@echo $this->endSection(); ?>

<@echo $this->section('footer'); ?>
   <@echo $this->renderSection('footer'); ?>

   <!-- add runtime changes to the footer section here -->
<@echo $this->endSection(); ?>