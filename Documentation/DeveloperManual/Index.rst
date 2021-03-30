.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _developer-manual:

For developers
==================

The extension ships with a PSR-14 event allowing developers to access the user data once the process is finished. The event is integrated before delivering the gated content in file "Classes/Controller/GateController.php", function deliverGatedContent()

.. code-block:: php

   <?php
       $this->eventDispatcher->dispatch(new ProcessUserDataEvent($userData));
   ?>

Registering an event listener
------------------------------

.. code-block:: yaml

   services:
     Vendor\MyExtension\EventListener\MyListener:
       tags:
         - name: event.listener
           identifier: 'myListener'
           event: Vendor\SomeExtension\Something\SpecificEvent
           before: 'some-other-identigit cfier, and-another-identifier'
