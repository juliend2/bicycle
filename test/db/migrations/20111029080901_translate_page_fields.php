<?php
function translate_page_fields_20111029080901 () {
  return array(
    rename_column('pages', 'title', 'title_fr'),
    add_column('pages', 'title_en', 'string'),
    rename_column('pages', 'slug', 'slug_fr'),
    add_column('pages', 'slug_en', 'string'),
    rename_column('pages', 'content', 'content_fr'),
    add_column('pages', 'content_en', 'text'),
  );
}
