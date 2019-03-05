var AdminExile = new Class({
    Implements:[Events,Options],
    options: {
        invalidChars: {
            32 : 'SPACE',       // Space
            34 : 'QUOTE',       // "
            35 : 'POUND',       // #
            36 : 'DOLLAR',      // $
            37 : 'PERCENT',     // %
            38 : 'AMPERSAND',   // &
            43 : 'PLUS',        // +
            44 : 'COMMA',       // ,
            47 : 'FORWARDSLASH',// /
            58 : 'COLON',       // :
            59 : 'SEMICOLON',   // ;
            60 : 'LESSTHAN',    // <
            61 : 'EQUALS',      // =
            62 : 'GREATERTHAN', // >
            63 : 'QUESTION',    // ?
            64 : 'AT',          // @
            91 : 'LEFTBRACKET', // [
            92 : 'BACKSLASH',   // \
            93 : 'RIGHTBRACKET',// ]
            94 : 'CARAT',       // ^
            96 : 'GRAVE',       // `
            123: 'LEFTCURLY',   // {
            125: 'RIGHTCURLY',  // }
            124: 'PIPE',        // |
            126: 'TILDE'        // ~
        },
        version:2.5,
        parentelement:'li',
        gmp:false
    },
    initialize:function(options){
        var self = this;
        self.setOptions(options);
        Core.JText.load();
        // identify parent element for show/hide
        if(version_compare(options.version,3,'<')) {
            self.options.parentelement = 'li';
        } else {
            self.options.parentelement = 'div.control-group';
        }
        // set up input filter
        ['jform_params_key','jform_params_keyvalue'].each(function(item){
            document.id(item).addEvent('keyup',function(){
                self.testInput(item,document.id(item).value);
            });
        });
        // set up param hiders
        document.id('jform_params_twofactor').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.twoFactorParams();
            });
        });
        self.twoFactorParams();
        document.id('jform_params_tmpwhitelist').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.tmpWhitelistParams();
            });
        });
        self.tmpWhitelistParams();
        
        // set up redirects
        document.id('jform_params_redirect').addEvent('keyup',function(){
            self.redirect404();
        });
        self.redirect404();
        
        document.id('jform_params_frontrestrict').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.frontendRestrictParams();
            });
        });
        self.frontendRestrictParams();
        document.id('jform_params_maillink').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.mailLinkParams();
            });
        });
        self.mailLinkParams();
        document.id('jform_params_ipsecurity').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.ipSecurityParams();
            });
        });
        self.ipSecurityParams();
        document.id('jform_params_bruteforce').getChildren('input').each(function(el){
            el.addEvent('click',function(){
                self.bruteForceParams();
            });
        });
        self.bruteForceParams();
        $$('.removeblock').each(function(el){
            el.addEvent('click',function(e){
                e.preventDefault();
                uri = new URI(window.location);
                uri.set('query',undefined);
//                data = JSON.decode(this.getProperty('data-block'));
                data = JSON.parse(this.getProperty('data-block'));
                data['adminexile_removeblock']='true';
                var removeRequest = new Request.JSON({
                    url:uri.toString(),
                    onSuccess:function(response){
                        if(response.success) {
                            el.getParent('tr').destroy();
                        }
                    }
                }).get(data);
            });
        });
        
        // setup IP inputs
        $$('.ipsecurity.list').each(function(el){            
            if(!/[\[\]]/.test(el.value)) self.upgradeIPInput(el); // upgrade ip inputs
            $$('button.'+el.id)[0].addEvent('click',function(e){
                e.preventDefault();
                self.promptIP(el);
            });
            self.initIP(el);
        });
        
        self.yourURL();
    },
    promptIP:function(el,pre) {
        var self = this;
        if(!pre) pre = '';
        
        var response = prompt(Core.JText._('PLG_SYS_ADMINEXILE_POPUP_NEW_IPV'+(self.options.gmp?'46':'4')),pre);
        if(response !== null) {
            if(self.validIPv46(response)) {
                response = response.trim();
                if(pre.length) {
                    self.removeIP(el,pre,false);
                }
//                var iplist = JSON.decode(el.value);
                var iplist = JSON.parse(el.value);
                if(!iplist.contains(response)) {
                    self.addIP(el,response,false);
                } else {
                    alert(Core.JText._('PLG_SYS_ADMINEXILE_POPUP_DUPLICATE_ADDRESS'));
                }
            } else {
                alert(Core.JText._('PLG_SYS_ADMINEXILE_POPUP_INVALID_ADDRESS'));
            }
        }
        return false;
    },
    initIP:function(el) {
        var self = this;
        var table = $$('table.'+el.id)[0];
        table.empty();
        var tr = new Element('tr').inject(table,'bottom');
        var ip = new Element('th',{html:Core.JText._('PLG_SYS_ADMINEXILE_TH_IP')}).inject(tr,'bottom');
        var actions = new Element('th',{html:Core.JText._('PLG_SYS_ADMINEXILE_TH_ACTIONS')}).inject(tr,'bottom');
        if(el.id === 'jform_params_blacklist') {
            var attempts = new Element('th',{html:Core.JText._('PLG_SYS_ADMINEXILE_TH_ATTEMPTS')}).inject(tr,'bottom');
            var lastattempt = new Element('th',{html:Core.JText._('PLG_SYS_ADMINEXILE_TH_LASTATTEMPT')}).inject(tr,'bottom');
            var options = new Element('th',{html:Core.JText._('PLG_SYS_ADMINEXILE_TH_OPTIONS')}).inject(tr,'bottom');
        }
//        var ipvalues = JSON.decode(el.value);
        var ipvalues = JSON.parse(el.value);
        ipvalues = ipvalues.sort();
        ipvalues.each(function(ip){
            self.addIP(el,ip,true);
        });
    },
    removeIP:function(el,ip,confdelete) {
        var self = this;
        if(confdelete === undefined) confdelete = true;
        if(confdelete)
            var conf = confirm('Are you sure?');
        if(!confdelete || conf) {
//            var iplist = JSON.decode(el.value);
            var iplist = JSON.parse(el.value);
            delete iplist[iplist.indexOf(ip)];
            iplist = iplist.filter(Boolean);
//            el.value = JSON.encode(iplist);
            el.value = JSON.stringify(iplist);
            self.initIP(el);
        }
        return false;
    },
// future use - when com_ajax becomes available for 2.5 in the JED this will be completed
    updateAttempts:function(attempts){
        var self = this;
        Object.each(attempts,function(o,i){
            window.attempts = document.id(self.ipID(i,'attempts'));
//            console.log(typeof window.attempts);
//            var attempts = document.id(self.ipID(i,'attempts'));
//            console.log(attempts);
//            attempts.empty();
//            attempts.appendText(o.attempts);
//            console.log(o);
        });
    },
    ipID:function(ip,type) {
        var self = this;
        return 'blacklist_'+ip.replace(/[\.\/]/g,'_')+'_'+type;
    },
    addIP:function(el,ip,init) {
        var self = this;
        if(!init) {
//            iplist = JSON.decode(el.value);
            iplist = JSON.parse(el.value);
            iplist.push(ip);
            iplist = iplist.sort();
//            el.value = JSON.encode(iplist);
            el.value = JSON.stringify(iplist);
            self.initIP(el);
        } else {
            var table = $$('table.'+el.id)[0];
            var tr = new Element('tr').inject(table,'bottom');
            var iptd = new Element('td',{html:ip,style:'text-align:left;'}).inject(tr,'bottom');
            var buttons = new Element('td').inject(tr,'bottom');
            var editip = new Element('button',{
                html:Core.JText._('PLG_SYS_ADMINEXILE_BUTTON_EDIT_IP'),
                events:{
                    click:function(e){
                        e.preventDefault();
                        self.promptIP(el,ip);
                        return false;
                    }
                }
            }).inject(buttons,'bottom');
            var deleteip = new Element('button',{
                html:Core.JText._('PLG_SYS_ADMINEXILE_BUTTON_DELETE_IP'),
                events:{
                    click:function(e){
                        e.preventDefault();
                        self.removeIP(el,ip);
                        return false;
                    }
                }
            }).inject(buttons,'bottom');
            
            if(el.id === 'jform_params_blacklist') {
                var attempts = new Element('td',{id:self.ipID(ip,'attempts')}).inject(tr,'bottom');
                var lastattempt = new Element('td',{id:self.ipID(ip,'lastattempt')}).inject(tr,'bottom');
                var options = new Element('td').inject(tr,'bottom');
                if(window.plg_sys_adminexile_blacklist.hasOwnProperty(ip)) {
                    attempts.appendText(window.plg_sys_adminexile_blacklist[ip].attempts);
                    if(window.plg_sys_adminexile_blacklist[ip].hasOwnProperty('addresses')) {
                        Object.each(window.plg_sys_adminexile_blacklist[ip].addresses,function(ai,a){
                            var cr = new Element('tr').inject(table,'bottom');
                            new Element('td',{html:a}).inject(cr,'bottom');
                            new Element('td').inject(cr,'bottom');
                            new Element('td',{html:ai.attempts}).inject(cr,'bottom');
                            new Element('td',{html:ai.lastattempt}).inject(cr,'bottom');
                            var cbuttons = new Element('td').inject(cr,'bottom');
                            var button = self.clearButton(a,ip,ai.firstattempt,ai.attempts).inject(cbuttons,'bottom');                            
                            button.addEvent('click',function(e){
                                e.preventDefault();
                                uri = new URI(window.location);
                                uri.set('query',undefined);
//                                data = JSON.decode(this.getProperty('data-block'));
                                data = JSON.parse(this.getProperty('data-block'));
                                data['adminexile_removeblock']='true';
                                var parentattemptstd = document.id(self.ipID(this.getProperty('data-parent'),'attempts'));
                                var parentcount = parseInt(parentattemptstd.get('html'));
                                var thiscount = parseInt(this.getProperty('data-attempts'));
                                var removeRequest = new Request.JSON({
                                    url:uri.toString(),
                                    onSuccess:function(response){
                                        if(response.success) {    
                                            delete window.plg_sys_adminexile_blacklist[ip].addresses[a];
                                            var parentcount = parseInt(parentattemptstd.get('html'));
                                            parentcount-= thiscount;
                                            parentattemptstd.set('html',parentcount);
                                            button.getParent('tr').destroy();
                                        }
                                    }
                                }).get(data);
                            });
// '<button class="btn btn-mini removeblock hasTip" data-block="'.htmlentities(json_encode(array('ip'=>$match->ip,'firstattempt'=>$match->firstattempt))).'" data-toggle="tooltip" title="'.JText::_('JACTION_DELETE').'"><i class="icon-trash"></i>'.$deletetext.'</button>';
                        });
                    } else {
                        lastattempt.appendText(window.plg_sys_adminexile_blacklist[ip].lastattempt);
                        var button = self.clearButton(ip,false,window.plg_sys_adminexile_blacklist[ip].firstattempt,window.plg_sys_adminexile_blacklist[ip].attempts).inject(options,'bottom');
                        button.addEvent('click',function(e){
                            e.preventDefault();
                            attemptstd = document.id(self.ipID(ip,'attempts'));
                            lastattempttd = document.id(self.ipID(ip,'lastattempt'));
                            uri = new URI(window.location);
                            uri.set('query',undefined);
//                            data = JSON.decode(this.getProperty('data-block'));
                            data = JSON.parse(this.getProperty('data-block'));
                            data['adminexile_removeblock']='true';
                            var removeRequest = new Request.JSON({
                                url:uri.toString(),
                                onSuccess:function(response){
                                    if(response.success) {    
                                        delete window.plg_sys_adminexile_blacklist[ip];
                                        self.initIP(document.id('jform_params_blacklist'));
                                    }
                                }
                            }).get(data);
                        });
                    }
                } else {
                    attempts.appendText(0);
                }
            }
        }
        return false;
    },
    clearButton:function(ip,parent,firstattempt,attempts){
        var button = new Element('button',{
            'data-parent':parent?parent:null,
            'data-attempts':attempts,
//            'data-block':JSON.encode({ip:ip,firstattempt:firstattempt}),
            'data-block':JSON.stringify({ip:ip,firstattempt:firstattempt}),
            'data-toggle':'tooltip',
            'title':Core.JText._('JACTION_DELETE')
        });
        var icon = new Element('span',{'class':'icon-trash'}).inject(button,'bottom');
        button.appendText(Core.JText._('JACTION_DELETE'));
        return button;
    },
    yourURL:function(){        
        var adminurl = new URI(window.location);  
        if(document.id('jform_params_twofactor0').checked) {       
            adminurl.setData({});           
            adminurl.set('query',document.id('jform_params_key').value);
        } else {
            var data = {};
            data[document.id('jform_params_key').value]=document.id('jform_params_keyvalue').value;
            adminurl.setData(data);
        }
        target = document.id('jform_params_url-lbl').getParent(this.options.parentelement).getElements('span.after')[0];
        target.empty();
        var anchor = new Element('a',{href:adminurl,html:adminurl}).inject(target,'top');
    },
    testInput:function(type,str){
        var self = this;
        if(type === 'jform_params_key' && (/^[0-9]+$/.test(str))) {
            document.id(type).value='';
            alert(Core.JText._('PLG_SYS_ADMINEXILE_MESSAGE_NOTNUMERIC'));
            return;
        }
        if(!(/^[\040-\177]*$/.test(str))) {
            while(!(/^[\040-\177]*$/.test(str))) for(i=0;i<=(str.length-1);i++) 
                if(!(/^[\040-\177]*$/.test(str.charAt(i)))) 
                    document.id(type).value = str.replace(str.charAt(i),''); 
            alert(Core.JText._('PLG_SYS_ADMINEXILE_MESSAGE_INVALIDASCII'));
            return;
        }
        for(i=0;i<=(str.length-1);i++) {
            if(self.options.invalidChars.hasOwnProperty(str.charCodeAt(i))) {
                document.id(type).value = str.replace(str.charAt(i),'');
                alert(Core.JText._('PLG_SYS_ADMINEXILE_MESSAGE_INVALIDCHAR') + "\n\n" + self.validCharsMessage());
                return;
            }
        }
        self.yourURL();
    },
    validCharsMessage:function(){
        var self = this;
        var str = [];
        Object.each(self.options.invalidChars,function(value,key){
            str.push(String.fromCharCode(key)+ '\t:\t' + Core.JText._('PLG_SYS_ADMINEXILE_CHAR_'+value));
        });
        str = str.join('\n');
        return str;
    },
    twoFactorParams:function(){
        var self = this;
        if(document.id('jform_params_twofactor1').checked) {
            document.id('jform_params_keyvalue').getParent(self.options.parentelement).show();
        } else {
            document.id('jform_params_keyvalue').getParent(self.options.parentelement).hide();            
        }
        self.yourURL();
    },
    tmpWhitelistParams:function(){
        var self = this;
        if(document.id('jform_params_tmpwhitelist1').checked) {
            document.id('jform_params_tmpperiod').getParent(self.options.parentelement).show();
        } else {
            document.id('jform_params_tmpperiod').getParent(self.options.parentelement).hide();            
        }
    },
    redirect404:function(){
        var self = this;
        if(document.id('jform_params_redirect').value === '{404}') {
            document.id('jform_params_fourofour').getParent(self.options.parentelement).show();
        } else {
            document.id('jform_params_fourofour').getParent(self.options.parentelement).hide();            
        }
    },
    frontendRestrictParams:function(){
        var self = this;
        var el = document.id('jformparamsrestrictgroup')?document.id('jformparamsrestrictgroup'):document.id('jform_params_restrictgroup');
        if(document.id('jform_params_frontrestrict1').checked) {
            el.getParent(self.options.parentelement).show();
        } else {            
            el.getParent(self.options.parentelement).hide();   
        }
    },
    mailLinkParams:function(){
        var self = this;
        var el = document.id('jformparamsmaillinkgroup')?document.id('jformparamsmaillinkgroup'):document.id('jform_params_maillinkgroup');
        if(document.id('jform_params_maillink1').checked) {
            el.getParent(self.options.parentelement).show();
        } else {
            el.getParent(self.options.parentelement).hide();            
        }
    },
    ipSecurityParams:function(){
        var self = this;
        if(document.id('jform_params_ipsecurity1').checked) {
            $$('.ipsecurity').each(function(el){el.getParent(self.options.parentelement).show();});
        } else {
            $$('.ipsecurity').each(function(el){el.getParent(self.options.parentelement).hide();});          
        }
    },
    bruteForceParams:function(){
        var self = this;
        if(document.id('jform_params_bruteforce1').checked) {
            $$('.bruteforce').each(function(el){el.getParent(self.options.parentelement).show();});
        } else {
            $$('.bruteforce').each(function(el){el.getParent(self.options.parentelement).hide();});          
        }
    },
    validIPv46:function(ip) {
        var self = this;
        var regex;
        if(self.options.gmp) {
            regex = new RegExp('(^\s*((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(/(3[012]|[12]?[0-9]))?)\s*$)|(^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$)','i');
        } else {
            regex = new RegExp('(^\s*((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(/(3[012]|[12]?[0-9]))?)\s*$)','i');
        }
        return regex.test(ip);
    },
    upgradeIPInput:function(el) {
        var addresses = el.value.match(/[^\r\n]+/g);
        addresses.each(function(a,i){addresses[i]=a.trim();});
        addresses = addresses.sort();
//        el.value=JSON.encode(addresses);
        el.value=JSON.stringify(addresses);
    }
});
function version_compare (v1, v2, operator) {
  // From: http://phpjs.org/functions
  // +      original by: Philippe Jausions (http://pear.php.net/user/jausions)
  // +      original by: Aidan Lister (http://aidanlister.com/)
  // + reimplemented by: Kankrelune (http://www.webfaktory.info/)
  // +      improved by: Brett Zamir (http://brett-zamir.me)
  // +      improved by: Scott Baker
  // +      improved by: Theriault
  // *        example 1: version_compare('8.2.5rc', '8.2.5a');
  // *        returns 1: 1
  // *        example 2: version_compare('8.2.50', '8.2.52', '<');
  // *        returns 2: true
  // *        example 3: version_compare('5.3.0-dev', '5.3.0');
  // *        returns 3: -1
  // *        example 4: version_compare('4.1.0.52','4.01.0.51');
  // *        returns 4: 1
  // BEGIN REDUNDANT
  this.php_js = this.php_js || {};
  this.php_js.ENV = this.php_js.ENV || {};
  // END REDUNDANT
  // Important: compare must be initialized at 0.
  var i = 0,
    x = 0,
    compare = 0,
    // vm maps textual PHP versions to negatives so they're less than 0.
    // PHP currently defines these as CASE-SENSITIVE. It is important to
    // leave these as negatives so that they can come before numerical versions
    // and as if no letters were there to begin with.
    // (1alpha is < 1 and < 1.1 but > 1dev1)
    // If a non-numerical value can't be mapped to this table, it receives
    // -7 as its value.
    vm = {
      'dev': -6,
      'alpha': -5,
      'a': -5,
      'beta': -4,
      'b': -4,
      'RC': -3,
      'rc': -3,
      '#': -2,
      'p': 1,
      'pl': 1
    },
    // This function will be called to prepare each version argument.
    // It replaces every _, -, and + with a dot.
    // It surrounds any nonsequence of numbers/dots with dots.
    // It replaces sequences of dots with a single dot.
    //    version_compare('4..0', '4.0') == 0
    // Important: A string of 0 length needs to be converted into a value
    // even less than an unexisting value in vm (-7), hence [-8].
    // It's also important to not strip spaces because of this.
    //   version_compare('', ' ') == 1
    prepVersion = function (v) {
      v = ('' + v).replace(/[_\-+]/g, '.');
      v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');
      return (!v.length ? [-8] : v.split('.'));
    },
    // This converts a version component to a number.
    // Empty component becomes 0.
    // Non-numerical component becomes a negative number.
    // Numerical component becomes itself as an integer.
    numVersion = function (v) {
      return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
    };
  v1 = prepVersion(v1);
  v2 = prepVersion(v2);
  x = Math.max(v1.length, v2.length);
  for (i = 0; i < x; i++) {
    if (v1[i] == v2[i]) {
      continue;
    }
    v1[i] = numVersion(v1[i]);
    v2[i] = numVersion(v2[i]);
    if (v1[i] < v2[i]) {
      compare = -1;
      break;
    } else if (v1[i] > v2[i]) {
      compare = 1;
      break;
    }
  }
  if (!operator) {
    return compare;
  }

  // Important: operator is CASE-SENSITIVE.
  // "No operator" seems to be treated as "<."
  // Any other values seem to make the function return null.
  switch (operator) {
  case '>':
  case 'gt':
    return (compare > 0);
  case '>=':
  case 'ge':
    return (compare >= 0);
  case '<=':
  case 'le':
    return (compare <= 0);
  case '==':
  case '=':
  case 'eq':
    return (compare === 0);
  case '<>':
  case '!=':
  case 'ne':
    return (compare !== 0);
  case '':
  case '<':
  case 'lt':
    return (compare < 0);
  default:
    return null;
  }
}
if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
    document._oldGetElementById = document.getElementById;
    document.getElementById = function(id) {
        if(id === undefined || id === null || id === '') {
            return undefined;
        }
        return document._oldGetElementById(id);
    };
}
window.addEvent('domready',function(){
    var ae = new AdminExile(window.plg_sys_adminexile_config);
});