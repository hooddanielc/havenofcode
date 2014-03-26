<?php

  class Module {
    // override to put mustache static text onto page
    public static $text = [];
    // override to put css this module depends on
    public static $css = [];
    // override to put javascript this module depends on
    public static $js = [];

    private function genCls(&$taboo, &$classes, &$the_class) {
      $props = get_object_vars($the_class);
      foreach($props as &$cls) {
        if(is_subclass_of($cls, 'Module')) {
          $this->genCls($taboo, $classes, $cls);
          if(!in_array(get_class($cls), $taboo)) {
            $taboo[] = get_class($cls);
            $classes[] = $cls;
          }
        }
      }
    }

    protected function getDependencies() {
      $classes = [];
      $taboo = [];
      $this->genCls($taboo, $classes, $this);
      return $classes;
    }

    protected function getCss($classes) {
      $taboo = [];
      foreach($classes as &$cls) {
        foreach($cls::$css as &$css_file) {
          if(strpos($css_file, 'http://') !== false) {
            $taboo[] = '<link rel="stylesheet" href="'.$css_file.'" />';
          } else {
            $taboo[] = '<link rel="stylesheet" href="/static/'.$css_file.'" />';
          }
        }
      }
      foreach($this::$css as &$css_file) {
        if(!in_array($css_file, $taboo)) {
          if(!in_array($css_file, $taboo)) {
            if(strpos($css_file, 'http://') !== false) {
              $taboo[] = '<link rel="stylesheet" href="'.$css_file.'" />';
            } else {
              $taboo[] = '<link rel="stylesheet" href="/static/'.$css_file.'" />';
            }
          }
        }
      }
      return join("\n", $taboo);
    }

    protected function getJs($classes) {
      $taboo = [];
      foreach($classes as &$cls) {
        foreach($cls::$js as &$js_file) {
          if(!in_array($js_file, $taboo)) {
            $taboo[] = '<script type="text/javascript" src="/static/'.$js_file.'"></script>';
          }
        }
      }
      foreach($this::$js as &$js_file) {
        if(!in_array($js_file, $taboo)) {
          $taboo[] = '<script type="text/javascript" src="/static/'.$js_file.'"></script>';
        }
      }
      return join("\n", $taboo);
    }

    protected function getMustache($classes) {
      $taboo = [];
      foreach($classes as &$cls) {
        foreach($cls::$text as $k => $v) {
          if(!in_array($k, $taboo)) {
            $taboo[$k] = file_get_contents('static/'.$v, $use_include_path = true);
          }
        }
      }
      foreach($this::$text as $k => $v) {
        if(!in_array($k, $taboo)) {
          $taboo[$k] = file_get_contents('static/'.$v, $use_include_path = true);
        }
      }
      return $taboo;
    }
  }

  class Page extends Module {

    /*
    * override to put json data in app.data var
    * * * */
    public function getData() {
      return NULL;
    }

    /*
    * override to put custom title in page
    * * * */
    public function getTitle() {
      return 'Haven of Code';
    }

    /*
    * override to put custom page description
    * * * */
    public function getDescription() {
      return 'Welcome to Haven of Code';
    }

    public function render() {
      // topologically sorts dependencies
      $classes = $this->getDependencies();
      $css = $this->getCss($classes);
      $js = $this->getJs($classes);
      $mustache = $this->getMustache($classes);
      $data = $this->getData();
      ?>
        <!doctype html>
        <html lang="en">
          <head>
            <meta charset="utf-8">
            <title><?php echo $this->getTitle(); ?></title>
            <meta name="description" content="<?php echo $this->getDescription(); ?>">
            <meta name="author" content="Daniel Hood">
            <?php echo $css; ?>
          </head>
          <body>
            <script>
              var app = {
                modules: {}
              };
              app.mustache = <?php echo json_encode($mustache); ?>;
              app.data = <?php echo json_encode($data); ?>;
            </script>
            <?php echo $js; ?>
          </body>
        </html>
      <?php
    }
  }

?>
