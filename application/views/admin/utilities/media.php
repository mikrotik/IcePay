<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div id="elfinder"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" charset="utf-8">
  $().ready(function() {
    var _locale = locale;
    if(locale == 'pt'){
      _locale = 'pt_BR';
    }
    var elf = $('#elfinder').elfinder({
      url : admin_url+'utilities/elfinder_init',
      lang: _locale,
      height:700,
      uiOptions : {
    // toolbar configuration
    toolbar : [
    ['back', 'forward'],
        ['mkdir', 'mkfile', 'upload'],
        ['open', 'download', 'getfile'],
        ['quicklook'],
        ['copy', 'paste'],
        ['rm'],
        ['duplicate', 'rename', 'edit'],
        ['extract', 'archive'],
        ['search'],
        ['view'],
        ['info'],
        ]
      }
    }).elfinder('instance');
  });
</script>
</body>
</html>
