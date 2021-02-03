#
# Table structure for table 'tx_gatedcontent_domain_model_userdata'
#
CREATE TABLE tx_gatedcontent_domain_model_userdata (
	first_name varchar(255) DEFAULT '' NOT NULL,
	last_name varchar(255) DEFAULT '' NOT NULL,
	company varchar(255) DEFAULT '' NOT NULL,
	telephone varchar(255) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	zip varchar(255) DEFAULT '' NOT NULL,
	city varchar(255) DEFAULT '' NOT NULL,
	identifier varchar(255) DEFAULT '' NOT NULL,
	newsletter_subscription int(10) unsigned DEFAULT '0' NOT NULL
);

#
# Extend tt_content
#
CREATE TABLE tt_content (
    tx_gatedcontent_header varchar(255) DEFAULT '' NOT NULL,
    tx_gatedcontent_subheader varchar(255) DEFAULT '' NOT NULL,
    tx_gatedcontent_description text,
    tx_gatedcontent_image int(11) unsigned NOT NULL default '0'
);
