.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _introduction:

============
Introduction
============

This TYPO3 extension grants access to a protected page or document after entering personal data.

Description
===========

This extension implements a content element that provides access to a protected page or document.

The content element provides a form to request the user data, a headline, a subheader, a description and an image.
Depending on the selected layout minimal, one or two columns are displayed. We recommend to use the extension together with the `bootstrap framework <https://getbootstrap.com/>`__.

Access to the restricted page or document is granted after a configurable process including double-opt-in via email and optional a confirmation step by an administrator.




Features
========

- Double opt in for user
- Admin confirmation for system administrators
- Saving of user data to the database (if desired)
- Notifications of administrators and users
- Usage of JWT web token (https://jwt.io/) for granting access and communication with the user
