<?php $tables['authors'] = array(
'id' => array('type' => 'int', 'modules' => array('personal-informations'), 'name' => __('ID', 'audiobooks-authors-and-narrators'), 'width' => 5),
'first_name' => array('type' => 'text', 'modules' => array('personal-informations'), 'name' => __('First name', 'audiobooks-authors-and-narrators'), 'width' => 12, 'searchby' => __('the first name', 'audiobooks-authors-and-narrators')),
'last_name' => array('type' => 'text', 'modules' => array('personal-informations'), 'name' => __('Last name', 'audiobooks-authors-and-narrators'), 'width' => 12, 'searchby' => __('the last name', 'audiobooks-authors-and-narrators')),
'email_address' => array('type' => 'text', 'modules' => array('personal-informations'), 'name' => __('Email address', 'audiobooks-authors-and-narrators'), 'width' => 15, 'searchby' => __('the email address', 'audiobooks-authors-and-narrators')),
'description' => array('type' => 'text', 'modules' => array('personal-informations'), 'name' => __('Description', 'audiobooks-authors-and-narrators'), 'width' => 18, 'searchby' => __('the description', 'audiobooks-authors-and-narrators')),
'audiobooks_url' => array('type' => 'text', 'modules' => array('personal-informations'), 'name' => __('Audiobooks URL', 'audiobooks-authors-and-narrators'), 'width' => 18),
'profile_url' => array('type' => 'text', 'modules' => array('personal-informations'), 'name' => __('Profile URL', 'audiobooks-authors-and-narrators'), 'width' => 18),
'date' => array('type' => 'datetime', 'modules' => array('personal-informations'), 'name' => __('Registration date', 'audiobooks-authors-and-narrators'), 'width' => 18),
'date_utc' => array('type' => 'datetime', 'modules' => array('personal-informations'), 'name' => __('Registration date (UTC)', 'audiobooks-authors-and-narrators'), 'width' => 18));

$tables['narrators'] = $tables['authors'];