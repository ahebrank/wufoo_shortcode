<div id="wufoo-${params['formhash']">
Fill out my <a href="https://${params['username']}.wufoo.com/forms/${params['formhash']}">online form</a>.
</div>
<div id="wuf-adv" style="font-family:inherit;font-size: small;color:#a7a7a7;text-align:center;display:block;">HTML Forms powered by <a href="http://www.wufoo.com">Wufoo</a>.</div>
<script type="text/javascript">var ${params['formhash']};(function(d, t) {
var s = d.createElement(t), options = {
'userName':'vtperformingarts',
'formHash':'${params['formhash']}',
'autoResize':${params['autoresize']},
'height':'${params['height']}',
'async':true,
'host':'wufoo.com',
'header':'${params['header']}',
'ssl':${params['ssl']}};
s.src = ('https:' == d.location.protocol ? 'https://' : 'http://') + 'www.wufoo.com/scripts/embed/form.js';
s.onload = s.onreadystatechange = function() {
var rs = this.readyState; if (rs) if (rs != 'complete') if (rs != 'loaded') return;
try { ${params['formhash']} = new WufooForm();${params['formhash']}.initialize(options);${params['formhash']}.display(); } catch (e) {}};
var scr = d.getElementsByTagName(t)[0], par = scr.parentNode; par.insertBefore(s, scr);
})(document, 'script');</script>
