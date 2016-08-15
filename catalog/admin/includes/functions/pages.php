<?php

  function tep_get_pages_name($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_name from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_name'];
  }

  function tep_get_pages_intro($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_intro from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_intro'];
  }

  function tep_get_pages_body($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_body from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_body'];
  }

  function tep_get_pages_body2($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_body2 from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_body2'];
  }

  function tep_get_pages_img_alt($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_img_alt from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_img_alt'];
  }

  function tep_get_pages_head_title_tag($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_head_title_tag from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_head_title_tag'];
  }

  function tep_get_pages_head_desc_tag($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_head_desc_tag from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_head_desc_tag'];
  }

  function tep_get_pages_head_keywords_tag($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_head_keywords_tag from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_head_keywords_tag'];
  }
  
  function tep_get_pages_surls_name($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select su.surls_name from " . TABLE_PAGES_DESCRIPTION . " pd, " . TABLE_SEO_URLS . " su where pd.pages_surls_id = su.surls_id and pd.pages_id = '" . (int)$page_id . "' and pd.language_id = '" . (int)$language_id . "'");
    if (tep_db_num_rows($page_query) == 1) {
      $page = tep_db_fetch_array($page_query);
      return $page['surls_name'];
    } else return '';
  }

  function tep_get_pages_h1($page_id, $language_id = 0) {
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    $page_query = tep_db_query("select pages_h1 from " . TABLE_PAGES_DESCRIPTION . " where pages_id = '" . (int)$page_id . "' and language_id = '" . (int)$language_id . "'");
    $page = tep_db_fetch_array($page_query);

    return $page['pages_h1'];
  }
?>
