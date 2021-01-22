<?php

return [
  'civi_flexmailer_embedimages' => [
    'group_name' => 'Advanced E-Mail Preferences',
    'group' => 'civiflexmailer',
    'name' => 'civi_flexmailer_embedimages',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 1,
    'title' => 'Embed images in emails (HTML inline images)',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'If selected, images get embedded into the email (inline images), not referenced as a link.',
    'help_text' => 'Enable if you want images to be embedded into the email (inline images), not referenced as a link. This increases the email size but displays images in the client right away.',
  ],
  'civi_flexmailer_embedimageslocal' => [
    'group_name' => 'Advanced E-Mail Preferences',
    'group' => 'civiflexmailer',
    'name' => 'civi_flexmailer_embedimageslocal',
    'type' => 'Boolean',
    'quick_form_type' => 'YesNo',
    'default' => 0,
    'title' => 'If embedding, embed only local images',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Only local hosted images will be embedded.',
    'help_text' => 'Enable if you only want local images to be embedded.',
  ]
];

?>
