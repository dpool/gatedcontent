.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. Set default language for code-blocks to TypoScript for this page!
.. highlight:: typoscript

.. _ts:

TypoScript
==========

Plugin settings
---------------
This section covers all settings, which can be defined in the plugin itself.

Properties
^^^^^^^^^^

.. container:: ts-properties

	==================================== ====================================== ==============
	Property                             Title                                   Type
	==================================== ====================================== ==============
	validityPeriod_                      Validity Period                         int
	==================================== ====================================== ==============

.. _tsValidityPeriod:

Validity Period
""""""""""
.. container:: table-row

   Property
         validityPeriod
   Data type
         int
   Description
         Define the validity period of the token

         ::

            plugin.tx_gatedcontent.settings.tokenService.validityPeriod = 3600


   Default
         3600