.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt

.. _installation:

Installation
============

The extension needs to be installed as any other extension of TYPO3 CMS:

   #. **Use composer(recommended):** Use `composer require dpool/gatedcontent`.

Latest version from git
-----------------------
You can get the latest version from git by using the git command:

.. code-block:: bash

   git clone https://github.com/dpool/gatedcontent.git

.. important::

   The master branch supports TYPO3 CMS 10 only.

Preparation: Include static TypoScript
--------------------------------------

The extension ships some TypoScript code which needs to be included.

#. Switch to the root page of your site.

#. Switch to the **Template module** and select *Info/Modify*.

#. Press the link **Edit the whole template record** and switch to the tab *Includes*.

#. Select **Gated content (gatedcontent)** at the field *Include static (from extensions):*

|img-gated_content_page_ts|
