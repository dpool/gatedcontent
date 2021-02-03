.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../../Includes.txt

.. Set default language for code-blocks to TypoScript for this page!
.. highlight:: xml

.. _flexform:

FlexForm
==========

Plugin settings
---------------
This section covers all settings, which can be defined in the plugin itself.

General
^^^^^^^^^^
In this section you can create some content for the form:

- **Header**
- **Subheader**
- **Description (RTE)**
- **Image**

Plugin Options
^^^^^^^^^^
In this section you can configure the form itself, the different options and the template layout.


Gated content
""""""""""
- Gated content identifier: Description for internal use only, can be used as an identifier in the emails
- Delivered content: The content type that should be granted access to: can be either a page or a file
- Page / File: depending on the delivered content option the input for either a file or a page is shown

|img-gated_content_ff_gatedcontent|

Form data
""""""""""
- Checkbox **"Lastname"**: Enables the firstname input field in the form
- Checkbox **"Firstnam"**: Enables the lastname input field in the form
- Checkbox **"Company"**: Enables the company input field in the form
- Checkbox **"Telephone"**: Enables the telephone input field in the form
- Checkbox **"Zip"**: Enables the zip input field in the form
- Checkbox **"City"**: Enables the city input field in the form
- RTE **"Legal notice"**: RTE field for displaying the checkbox to accept the terms and conditions

|img-gated_content_ff_form|

Finisher
""""""""""
- Checkbox **"Save user data to database"**: Enables persisting of the user data to the database
- Input **"Email recipient (e.g. admin)"**: Set the admin recipient address who will be informed as soon as the access is requested. If the value is empty, no mail will be send.
- Input **"Subject for email"**: Set the subject of the mail
- RTE **"Bodytext for email"**: Set the bodytext of the mail.


|img-gated_content_ff_finisher|

Double OptIn
""""""""""
- Checkbox **"Double OptIn"**: Enables the double-opt-in process for the user: confirming the mail address
- Input **"Subject for email to user"**: Set the subject of the mail
- RTE **"Bodytext for email to user"**: Set the bodytext of the mail.

|img-gated_content_ff_doubleOptIn|

Admin confirmation
""""""""""
- Checkbox **"adminConfirmation"**: Enables the admin confirmation process: admin needs to grant access to the requested ressource
- Input **"Email recipient"**: Set the admin recipient of the mail
- Input **"Subject for email to admin"**: Set the subject of the mail
- RTE **"Bodytext for email to admin"**: Set the bodytext of the mail.

|img-gated_content_ff_adminConfirmation|

Template
""""""""""
- Select **"Template Layout"**: Switch between the layouts 2 columns, 1 column or minimalist

|img-gated_content_ff_template|

Email Markers
^^^^^^^^^^
You can use some specific and some dynamic markers in the mail templates (RTE fields)

Specific Markers
""""""""""
- **{link}**: Generated the link tag, containing the link with the token to the depending process step
- **{identifier}**: Includes the identifier which is set in the tab "Gated content" (Gated content identifier)

Dynamic Markers
""""""""""
The dynamic markers depend on the userData model which can be extended as every other model in extbase. The default model comes with the specific getters and therefore those markers can be used:

- **{user.firstname}**
- **{user.lastname}**
- **{user.company}**
- **{user.telephone}**
- **{user.email}**
- **{user.zip}**
- **{user.city}**

In case you extend the model by f.e. a 'middlename' property and you have the getter getMiddlename(); you can use the maker **{user.middlename}** as well.
