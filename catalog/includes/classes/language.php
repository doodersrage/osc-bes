<?php
/*
  $Id: language.php,v 1.6 2003/06/28 16:53:09 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License

  browser language detection logic Copyright phpMyAdmin (select_lang.lib.php3 v1.24 04/19/2002)
                                   Copyright Stephane Garin <sgarin@sgarin.com> (detect_language.php v0.1 04/02/2002)
*/

  class language {
    var $languages, $catalog_languages, $browser_languages, $language, $link_string; // $link_string added by splautz for header links

    function language($lng = '') {
      $this->languages = array('ar' => 'ar([-_][[:alpha:]]{2})?|arabic',
                               'bg' => 'bg|bulgarian',
                               'br' => 'pt[-_]br|brazilian portuguese',
                               'ca' => 'ca|catalan',
                               'cs' => 'cs|czech',
                               'da' => 'da|danish',
                               'de' => 'de([-_][[:alpha:]]{2})?|german',
                               'el' => 'el|greek',
                               'en' => 'en([-_][[:alpha:]]{2})?|english',
                               'es' => 'es([-_][[:alpha:]]{2})?|spanish',
                               'et' => 'et|estonian',
                               'fi' => 'fi|finnish',
                               'fr' => 'fr([-_][[:alpha:]]{2})?|french',
                               'gl' => 'gl|galician',
                               'he' => 'he|hebrew',
                               'hu' => 'hu|hungarian',
                               'id' => 'id|indonesian',
                               'it' => 'it|italian',
                               'ja' => 'ja|japanese',
                               'ko' => 'ko|korean',
                               'ka' => 'ka|georgian',
                               'lt' => 'lt|lithuanian',
                               'lv' => 'lv|latvian',
                               'nl' => 'nl([-_][[:alpha:]]{2})?|dutch',
                               'no' => 'no|norwegian',
                               'pl' => 'pl|polish',
                               'pt' => 'pt([-_][[:alpha:]]{2})?|portuguese',
                               'ro' => 'ro|romanian',
                               'ru' => 'ru|russian',
                               'sk' => 'sk|slovak',
                               'sr' => 'sr|serbian',
                               'sv' => 'sv|swedish',
                               'th' => 'th|thai',
                               'tr' => 'tr|turkish',
                               'uk' => 'uk|ukrainian',
                               'tw' => 'zh[-_]tw|chinese traditional',
                               'zh' => 'zh|chinese simplified');

      $this->catalog_languages = array();
      $languages_query = tep_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
      while ($languages = tep_db_fetch_array($languages_query)) {
        $this->catalog_languages[$languages['code']] = array('id' => $languages['languages_id'],
                                                             'name' => $languages['name'],
                                                             'image' => $languages['image'],
                                                             'directory' => $languages['directory']);
      }

      $this->browser_languages = '';
      $this->language = '';
      $this->link_string = ''; // added by splautz for header links

      $this->set_language($lng);
    }

    function set_language($language) {
// modified by splautz to support setting by language id
//    if ( (tep_not_null($language)) && (isset($this->catalog_languages[$language])) ) {
//      $this->language = $this->catalog_languages[$language];
//    } else {
//      $this->language = $this->catalog_languages[DEFAULT_LANGUAGE];
//    }
      if ( (tep_not_null($language)) ) {
        if ( is_numeric($language) ) {
          foreach( $this->catalog_languages as $code => $catalog_language ) {
            if ( $language == $catalog_language['id'] ) {
              $this->language = $this->catalog_languages[$code];
              return;
            }
          }
        } elseif ( isset($this->catalog_languages[$language]) ) {
          $this->language = $this->catalog_languages[$language];
          return;
        }
      }
      $this->language = $this->catalog_languages[DEFAULT_LANGUAGE];
    }

    function get_browser_language() {
      $this->browser_languages = explode(',', getenv('HTTP_ACCEPT_LANGUAGE'));

      for ($i=0, $n=sizeof($this->browser_languages); $i<$n; $i++) {
        reset($this->languages);
        while (list($key, $value) = each($this->languages)) {
          if (eregi('^(' . $value . ')(;q=[0-9]\\.[0-9])?$', $this->browser_languages[$i]) && isset($this->catalog_languages[$key])) {
            $this->language = $this->catalog_languages[$key];
            break 2;
          }
        }
      }
    }

// added by splautz for header links
    function alt_languages(){ 
      global $request_type;
      $this->link_string = ''; 
      foreach ($this->catalog_languages as $code => $data){ 
          if ($this->language['id'] == $data['id']) continue; 
          $href = tep_href_link($_SERVER['PHP_SELF'], tep_get_all_get_params() . 'language=' . $code, $request_type, false); 
          $this->link_string .= '<link rel="alternate" type="text/html" href="'.$href.'" lang="'.$code.'"  hreflang="'.$code.'" title="'.$data['name'].' Translation" />' . "\n";             
      } 
    } 
  }
?>
