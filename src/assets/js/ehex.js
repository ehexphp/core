/* eslint-disable */
/* tslint:disable */
/*
    function  Class1(){}
    function  Object1(){}
    function  Array1(){}
    function  Style1(){}
    function  Page1(){}
    function  String1(){}
    function  Color1(){}
    function  Cookie1(){}
    function  Form1(){}
    function  Html1(){}
    function  Math1(){}
    function  Url1(){}
    function  Picture1(){}
    function  FileManager1(){}
    function  Ajax1(){}
    function  Popup1(){}
    function  Date1(){}
 */







// Debug Function
function dd(data, title){ console.log(title? title: '---Debug---', data); if(window.swal) swal(title? Object1.toCleanJsonString(title): 'Debug', Object1.toCleanJsonString(data)); }
function d(data){ console.log(title? title: '---Debug---', data); }




/********************************************************************************
 *                      JQUERY Function Like
 ********************************************************************************/
function enableJQueryLite(){

    /**
     * Get Single Element
     * $('h2') // will return single _&lt;h2&gt;_ element
     * @param query
     * @param parentContainer
     */
    window.$ = function (query, parentContainer) { parentContainer = parentContainer? parentContainer: document; parentContainer.querySelector(query)};
    /**
     * Get Multiple Element
     * $$('p', $('article'))
     * @param query
     * @param parentContainer
     */
    window.$$ = function (query, parentContainer) { parentContainer = parentContainer? parentContainer: document; parentContainer.querySelectorAll(query)};

    /**
     * This allows you to "forEach" a NodeList returned by querySelectorAll or $$
     * similar to jQuery.prototype.each
     * use: $$('li').each(callback)
     */
    Object.defineProperty(NodeList.prototype, 'each', { value: function (fn) {return Array.from(this).forEach(function(node, index) { fn(node, index)}); } });

    /**
     * append HTMLElement to another HTMLElement
     * like jQuery append()
     */
    HTMLElement.prototype.append = function (child) {
        if (child instanceof HTMLElement) {this.appendChild(child);return this}
        this.append(child);return this
    };

    /**
     * prepend HTMLElement to another HTMLElement
     * like jQuery prepend()
     */
    HTMLElement.prototype.prepend = function (sibling) {
        if (sibling instanceof HTMLElement) {this.parentNode.insertBefore(sibling, this);return this;}
        this.parentNode.insertBefore(sibling, this);
        return this;
    };


    /**
     * $('#el').html('')
     * @param string
     * @returns {*}
     */
    HTMLElement.prototype.html = function (string) {
        if (typeof string === 'undefined')  return this.innerHTML;
        this.innerHTML = string; return this
    };

    /**
     * single function to get and set innerText
     * get:  $('body').text()
     * set:  $('body').text('hi!')
     */
    HTMLElement.prototype.text = function (string) {
        if (typeof string === 'undefined')  return this.textContent;
        this.innerText = string; return this;
    };



    /**
     * single method to get/set/list HTMLElement dataset values
     * get:  $('div').data('color')     assuming <div data-color="..."></div>
     * set:  $('div').data('color', '#0099ff')
     */
    HTMLElement.prototype.data = function (key, value) {
        if (!value) {if (!key) { return this.dataset; } return this.dataset[key]; }
        this.dataset[key] = value; return this
    };

    /**
     * single method to get/set/list attributes of HTMLElement.
     * get argument id:     $('div').attr('id')
     * set argument id:     $('div').attr('id', 'post123')
     * list all arguments:  $('div').attr()  // Fuck yeah
     */
    HTMLElement.prototype.attr = function (key, value) {
        if(value && value.length>0) {this.setAttribute(key, value);  return this;}
        if (!key) { return this.attributes } return this.getAttribute(key);
    };

    /**
     * remove attribute from HTMLElement by key
     */
    HTMLElement.prototype.removeAttr = function (key) { this.removeAttribute(key); return this};

    /**
     * check whether a DOM node has a certain attribute.
     */
    HTMLElement.prototype.has = function(attribute) { return this.hasAttribute(attribute) };

    /**
     * $('#foo').remove()
     // or
     *   $$('div').each(element => element.remove())
     */
    HTMLElement.prototype.remove = function() { this.parentNode.removeChild(this) };

    /**
     * get a HTMLElement's parent node
     * use: $('h1').parent()
     */
    HTMLElement.prototype.parent = function () { return this.parentNode };

    // /**
    //  * Convenient shortcut
    //  * use:   define('property', { ...descriptor })
    //  * e.g now | single statement accessor that returns current time
    //         define('now', {
    //             get: Date.now
    //         })
    //  *  use setInterval(() => console.log(now), 10);
    //  */
    // Object.defineProperty(window, 'define', {
    //     value: function (property, ...meta) { meta.length === 2 ? Object.defineProperty(meta[0], property, meta[1]) : Object.defineProperty(window, property, meta[0])},
    //     writable: false,
    //     enumerable: true
    // })
}













/***********************************************************
 *  Class
 ***********************************************************/
function Object1(){}
Object1.extend = function(child, parent){
    var Temp=function(){};
    Temp.prototype=parent.prototype;
    child.prototype= new Temp();
    child.prototype.constructor=child;
};

Object1.isObject = function(obj) {
    if(typeof obj === 'string' || obj instanceof String || typeof obj === 'boolean' || typeof obj === 'number') return false;
    return ((obj instanceof Object && obj.constructor === Object) ||   (obj.indexOf("[object Object]")) );
};

Object1.fromJsonString = function(value) { try{ return Object1.isObject(value)? value : JSON.parse(value); } catch(e) { return value;} };

/**
 * cannot be parse with JSON.parse again because all quote will be removed
 * @param value
 * @returns {*}
 */
Object1.toCleanJsonString = function(value) {  return Object1.toJsonString(value).toString().replace(/"/g, "") };
Object1.toJsonString = function(value) {  if(!value) return ''; try{ return Object1.isObject(value)? JSON.stringify(value): value; } catch(e) { return null;} };

/**
 * Remove Null Attribute
 * @param values
 * @returns {*}
 */
Object1.removeNull = function (values) {
    var propNames = Object.getOwnPropertyNames(values);
    for (var i = 0; i < propNames.length; i++) {
        var propName = propNames[i];
        if (values[propName] === null || values[propName] === undefined) {
            delete values[propName];
        } else if (typeof values[propName] === 'object') {
            values[propName] = Object1.removeNull(values[propName]);
        } else if (values[propName].length < 1) {
            delete values[propName];
        }
    }
    return values;
};
Object1.isAttributeExists = function(object, attribute) {
    return (object)? (Object.prototype.hasOwnProperty.call(object, attribute) ||  object.hasOwnProperty(attribute)  || (typeof object[attribute] != 'undefined')): false;
};

Object1.getAttribute = function(object, attribute, defaultValue){
    return this.isAttributeExists(object, attribute)? object[attribute]: defaultValue;
};

Object1.extend = function(child, parent){
    var Temp=function(){};
    Temp.prototype=parent.prototype;
    child.prototype= new Temp();
    child.prototype.constructor=child;
};

Object1.fillDefaults = function(newObjectValue, defaultObject) {
    var keys = Object.keys(defaultObject);
    for (var i = 0; i < keys.length; i++) newObjectValue[keys[i]] = typeof defaultObject[keys[i]] === "object" ? Object1.fillDefaults(newObjectValue[keys[i]], defaultObject[keys[i]]) : (!(keys[i] in newObjectValue) ? defaultObject[keys[i]] : newObjectValue[keys[i]]);
    return newObjectValue;
};
Object1.toArray = function(object){
    return Object.keys(object).map(function(key){
        return [Number(key),  object[key]];
    });
};








/***********************************************************
 *  Array
 ***********************************************************/
function Array1(){}
// convert to array
Array1.makeArray = function(object) {
    if(!object) return [];
    return object instanceof Array ? object : [object];
};
Array1.uniqueArray = function(arr) {
    var result = [];
    for (var i in arr) if (result.indexOf(arr[i]) === -1)  result.push(arr[i]);
    return result;
};
Array1.pickOne = function (arrayList) { return arrayList[Math.floor(( Math.random() * arrayList.length ))]; };

Array1.contain = function (arrayList, $niddle){
    return arrayList.find(function($item){
        return ($item == $niddle);
    });
};
Array1.fromObject = function(object){ return Object1.toArray(object); };

/**
 *var elements = [ "apple", "apple", "orange", "apple", "banana"];
 console.log(  getAllDuplicateKeyCount(elements).apple )
 */
Array1.getAllDuplicateKeyCount = function(array){
    var counts = {};
    array.forEach(function(x) {  counts[x] = (counts[x] || 0) + 1;  });
    return counts;
}


/**
 *  console.log(groupBy(['one', 'two', 'three'], 'length'));
 // => {3: ["one", "two"], 5: ["three"]}
 */
Array1.groupBy = function(objectArray, property) {
    return objectArray.reduce(function(rv, x) {
        (rv[x[property]] = rv[x[property]] || []).push(x);
        return rv;
    }, {});
};






/***********************************************************
 *  Cookie
 ***********************************************************/
function Media1(){}
/**
 * Play an alarm sound
 */
Media1.alarm = function (path) {
    path = path? path: 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/123941/Yodel_Sound_Effect.mp3';
    var alarm = new Audio(path);
    alarm.play();
};


/***********************************************************
 *  Cookie
 ***********************************************************/
function Cookie1(){}

/**
 * get exists cookie value
 * @param name
 * @returns {string}
 */
Cookie1.get = function(name) {
    match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return decodeURIComponent(match[2]);
};

/**
 *
 * @param name
 * @param value
 * @param days
 * @param expiresDate
 *      [ e.g new Date().setTime(date.getTime()+(days*24*60*60*1000)); ]
 *      or date.setTime(date.getTime() + Number(hours) * 3600 * 1000)
 * @param path
 * @param domain
 * @param isSecure
 */
Cookie1.set = function (name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (Object1.toJsonString(value) || "")  + expires + "; path=/";
};

/**
 * Delete
 * @param name
 */
Cookie1.delete = function(name){
    var exp = new Date();
    exp.setTime (exp.getTime() - 1);
    var cval = Cookie1.get(name);
    document.cookie = name + '=' + cval + '; expires=' + exp.toGMTString();
};























/***********************************************************
 *  CSS STYLE
 ***********************************************************/
function Style1(){}
Style1.toggleDisplay = (function(className, state) {
    var elem = document.getElementsByClassName(className);
    for(var i=0; i<elem.length; i++){
        elem[i].style.display = state;
    }
});





/***********************************************************
 *  Page
 ***********************************************************/
function Page1(){}
Page1.printPage = function(containnerClassToHide) {
    $('.' + containnerClassToHide).hide();
    window.print();
    $('.' + containnerClassToHide).show();
};

Page1.printContainer = function(printContainerId) {
    Page1.addStyle('@media print {  body * { visibility: hidden; }  #'+printContainerId+' * { visibility: visible; }  #'+printContainerId+' { position: absolute; top: 0px; left: 0px; }  }');
    window.print();
};
Page1.printOnlyContainer = function(containnerIdToPrint) {
    var printContents = document.getElementById(containnerIdToPrint).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    setTimeout(function (){
        window.print();
        document.body.innerHTML = originalContents;
    }, 500);
};
/*Page1.printContainer2 = function(containnerIdToPrint) {
    // If you DO have form data that you need to keep, clone won't copy that, so you'll just need to grab all the form data and replace it after restore as so:
    var restorePage = $('body').html();
    var printContent = $('#' + containnerIdToPrint).clone();
    //var enteredtext = $('#text').val();
    $('body').empty().html(printContent);
    window.print();
    $('body').html(restorePage);
    //$('#text').html(enteredtext);
};*/

/**
 * Add Style to page
 *  E.g  Page1.addStyle(".some-element { color:yellow }");
 * @param style
 */
Page1.addStyle = function(style){
    var styleElem = document.createElement('style');
    styleElem.innerHTML = style;
    var ref = document.querySelector('body');
    ref.parentNode.insertBefore(styleElem, ref);
}


Page1.deviceType = function() {
    return {
        Android: function() { return navigator.userAgent.match(/Android/i);},
        BlackBerry: function() { return navigator.userAgent.match(/BlackBerry/i);},
        iOS: function() {return navigator.userAgent.match(/iPhone|iPad|iPod/i);},
        Opera: function() {return navigator.userAgent.match(/Opera Mini/i);},
        Windows: function() {return navigator.userAgent.match(/IEMobile/i);},
        any: function() {return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());},
        isMobile: function() {return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());}
    };
};
Page1.appendScript = function(filePath) {
    if ($('script[src="' + filePath + '"]').length > 0) return;

    var ele = document.createElement('script');
    ele.setAttribute("type", "text/javascript");
    ele.setAttribute("src", filePath);
    $('head').append(ele);
};
Page1.appendStyle = function(filePath) {
    if ($('link[href="' + filePath + '"]').length > 0)  return;

    var ele = document.createElement('link');
    ele.setAttribute("type", "text/css");
    ele.setAttribute("rel", "Stylesheet");
    ele.setAttribute("href", filePath);
    $('head').append(ele);
};

Page1.getFrameSize = function (){
    var frameWidth, frameHeight;
    if (self.innerWidth){
        frameWidth = self.innerWidth;
        frameHeight = self.innerHeight;
    }else if (document.documentElement && document.documentElement.clientWidth){
        frameWidth = document.documentElement.clientWidth;
        frameHeight = document.documentElement.clientHeight;
    }else if (document.body){
        frameWidth = document.body.clientWidth;
        frameHeight = document.body.clientHeight;
    }else return false;
    return {width:frameWidth, height:frameHeight};
};







/***********************************************************
 *  STRING
 ***********************************************************/
//String Class helper
function String1(){}

/**
 * execute a JavaScript function when I have its name as a string
 * fastest way is to use
 *  window["functionName"](arguments);
 *  Or window["My"]["Namespace"]["functionName"](arguments);
 * @param functionName
 * @param context
 * @returns {*}
 */
String1.toFunction = function(str, args) {
    var arr = str.split('.');
    var fn = window[ arr[0] ];
    for (var i = 1; i < arr.length; i++){ fn = fn[ arr[i] ]; }
    fn.apply(window, args);
};

/**
 * String1.mask('4567 6365 7987 3783', 4, 5) =  4567 **** **** 3783
 */
String1.maskOut = function(text, skipCount, leftCount){
    var first4 = text.substring(0, skipCount);
    var last5 = text.substring(text.length - leftCount);
    var mask = text.substring(skipCount, text.length - leftCount).replace(/\d/g,"*");
    return (first4 + mask + last5);
};


String1.isString = function(value){ return (typeof value === 'string'); };
String1.isEmpty = function(value){
    if (/^\s*$/.test(value)) return true;
    switch (value) {case "": case 0:case "0":case null:case false:case typeof this == "undefined":return true;default:return false;}
};

String1.replaceCount = function (search, replace, subject, count) {
    var i = 0, j = 0, temp = '', repl = '', sl = 0, fl = 0,
        f = [].concat(search),
        r = [].concat(replace),
        s = subject,
        ra = Object.prototype.toString.call(r) === '[object Array]',
        sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    }
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') { continue; }
        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {
                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
};

String1.replace =  function(str, search, replaceWith, caseInsensitive){
    return caseInsensitive? str.replace(/search/gi, replaceWith): str.replace(/search/g, replaceWith);
};



String1.convertFunctionToHereDoc = function(hereName, str){
    // Convert Comment To String
    /**
     * @param here
     * @param str
     * @returns {string}
     * @constructor
     *
     FuncToHereDoc("HERE", MyHereDocFunction); function MyHereDocFunction(){ /*HERE <p> This is written ing the HEREDOC, notice the multilines :D. </p> <p> HERE </p> <p> And Here </p> HERE* / }
     */
    var reobj = new RegExp("/\\*"+hereName+"\\n[\\s\\S]*?\\n"+hereName+"\\*/", "m");
    str = reobj.exec(str).toString();
    str = str.replace(new RegExp("/\\*"+hereName+"\\n",'m'),'').toString();
    return str.replace(new RegExp("\\n"+hereName+"\\*/",'m'),'').toString();
};
String1.removeQuoteFromString = function(string){
    string.trim().slice(1, string.length-1).replace(/\\n/g, '<br/>');
};
String1.lineToArray = function(data){
    return  data.split(/\r?\n/);
};
String1.slugify = function(text, maxLength){
    text = text.replace(/[^-\w\s]/g,'');
    text=text.replace(/^\s+|\s+$/g,'');
    text=text.replace(/[-\s]+/g,'-');
    text=text.toLowerCase();
    if(maxLength) text.substring(0,maxLength);
    return text;
};
String1.convertToCamelCase = function (str, $joinSeparator){
    if(!str) return '';
    else {
        var replaced = str.replace(/-|_/g, ' ').split(' ');
        var solution = [replaced[0]];
        for(var i = 1; i < replaced.length; i++) solution.push(replaced[i].substring(0,1).toUpperCase()+replaced[i].substring(1).toLowerCase());
    }
    return solution.join($joinSeparator? $joinSeparator: '');
};


/***********************************************************
 *  COLOR
 ***********************************************************/
function Color1(){}
Color1.colorLuminance = function(hex, lum){
    // Validate hex string
    hex = String(hex).replace(/[^0-9a-f]/gi, '')
    if (hex.length < 6) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2]
    }
    lum = lum || 0

    // Convert to decimal and change luminosity
    var rgb = '#'
    for (var i = 0; i < 3; i++) {
        var c = parseInt(hex.substr(i * 2, 2), 16)
        c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16)
        rgb += ('00' + c).substr(c.length)
    }

    return rgb
};

Color1.getColorCode = (function(hex) {
    // assumming hex = #89sfsd, it will return just 89sfsd
    hex = String(hex).replace(/[^0-9a-f]/gi, '');
    return hex;
});

// get straight color from text
Color1.toColor = function(str) {
    var hash = 0;
    for (var ii = 0; ii < str.length; ii++) {
        hash = str.charCodeAt(ii) + ((hash << 5) - hash);
    }
    var colour = '#';
    for (var i = 0; i < 3; i++) {
        var value = (hash >> (i * 8)) & 0xFF;
        colour += ('00' + value.toString(16)).substr(-2);
    }
    return colour;
};
Color1.random = function(){var n=6,s='#';while(n--){s+=(Math.random()*16|0).toString(16)}return s};





/***********************************************************
 *  DateTime
 ***********************************************************/
function Date1(){}
Date1.now = (function () {
    return performance.now || performance.mozNow || performance.msNow || performance.oNow || performance.webkitNow ||
        function () {
            return new Date().getTime();
        };
})();

Date1.getTimeStamp = function(){
    return Math.round((new Date()).getTime() / 1000);
};
//Date1.fromPhp = function(timestamp){ return new Date("<?= date('Y/m/d H:i:s'); ?>") ; };
Date1.chatRelativeTime = function(date) {
    var s = Math.floor((new Date() - date) / 1000),
        i = Math.floor(s / 31536000);
    if (i > 1) {
        return i + " yrs ago"
    }
    i = Math.floor(s / 2592000);
    if (i > 1) {
        return i + " mon ago"
    }
    i = Math.floor(s / 86400);
    if (i > 1) {
        return i + " dys ago"
    }
    i = Math.floor(s / 3600);
    if (i > 1) {
        return i + " hrs ago"
    }
    i = Math.floor(s / 60);
    if (i > 1) {
        return i + " min ago"
    }
    return (Math.floor(s) > 0 ? Math.floor(s) + " sec ago" : "just now")
};

Date1.fromTimeStamp = function(UNIX_timestamp){
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    var min = a.getMinutes();
    var sec = a.getSeconds();
    var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
    return time;
};

Date1.countDown = function(expire_date = "july 30, 2050 15:37:25", dayId='', hourId='', minuteId='', second='', timeUpCallback = null){
    let deadline = new Date(expire_date).getTime();
    let x = setInterval(function() {
        let currentTime = new Date().getTime();
        let t = deadline - currentTime;
        let days = Math.floor(t / (1000 * 60 * 60 * 24));
        let hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
        let minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((t % (1000 * 60)) / 1000);
        document.getElementById(dayId).innerHTML = days ;
        document.getElementById(hourId).innerHTML = hours;
        document.getElementById(minuteId).innerHTML = minutes;
        document.getElementById(second).innerHTML = seconds;
        if (t < 0) {
            clearInterval(x);
            if(timeUpCallback) timeUpCallback();
        }
    }, 1000);
};


/***********************************************************
 *  FORM
 ***********************************************************/
function Form1(){}
Form1.escapeHTML = (function(s) {
    var n = s;
    n = n.replace(/&/g, '&amp;');
    n = n.replace(/</g, '&lt;');
    n = n.replace(/>/g, '&gt;');
    n = n.replace(/"/g, '&quot;');

    return n;
});
Form1.submitForm = (function(event, or_FORM_ID){
    if(event) event.form.submit();
    else if(or_FORM_ID) document.getElementById(or_FORM_ID).submit();
});

/**
 * jQuery Redirect
 * @param {string} url - Url of the redirection
 * @param {Object|null} postDataValues - (optional) An object with the data to send. If not present will look for values as QueryString in the target url.
 * @param {string|null} method - (optional) The HTTP verb can be GET or POST (defaults to POST)
 * @param {string|null} target - (optional) The target of the form. "_blank" will open the url in a new window.
 * @param {boolean|null} traditional - (optional) This provides the same function as jquery's ajax function. The brackets are omitted on the field name if its an array.  This allows arrays to work with MVC.net among sample.
 * @param {boolean|null} redirectTop - (optional) If its called from a iframe, force to navigate the top window.
 *
 * EXAMPLE on (https://github.com/mgalante/jquery.redirect)
 *  Form1.sendPost("/login.php",{ user: "johnDoe", password: "12345"});
 */
Form1.sendPost = function(url, postDataValues, method, target, traditional, redirectTop){
    method = method? method: (postDataValues? 'POST':'GET');

    var getForm = function (url, keyValueArray, method, target, traditional) {
        method = (method && ["GET", "POST", "PUT", "DELETE"].indexOf(method.toUpperCase()) !== -1) ? method.toUpperCase() : 'POST';

        url = url.split("#");
        var hash = url[1] ? ("#" + url[1]) : "";
        url = url[0];

        if (!keyValueArray) {
            var obj = Url1.parse(url);
            url = obj.url;
            keyValueArray = obj.params;
        }

        keyValueArray = Object1.removeNull(keyValueArray);
        var form = $('<form>').attr("method", method).attr("action", url + hash);
        if (target) form.attr("target", target);

        var submit = form[0].submit;
        iterateValues(keyValueArray, [], form, null, traditional);
        return { form: form, submit: function () { submit.call(form[0]); } };
    };
    //Utility Functions


    //Private Functions
    var getInput = function (name, value, parent, array, traditional) {
        var parentString;
        if (parent.length > 0) {
            parentString = parent[0];
            var i;
            for (i = 1; i < parent.length; i += 1) {
                parentString += "[" + parent[i] + "]";
            }

            if (array) {
                if (traditional) name = parentString;
                else name = parentString + "[" + name + "]";
            } else {
                name = parentString + "[" + name + "]";
            }
        }
        return $("<input>").attr("type", "hidden").attr("name", name).attr("value", value);
    };

    var iterateValues = function (values, parent, form, isArray, traditional) {
        var i, iterateParent = [];
        Object.keys(values).forEach(function (i) {
            if (typeof values[i] === "object") {
                iterateParent = parent.slice();
                iterateParent.push(i);
                iterateValues(values[i], iterateParent, form, Array.isArray(values[i]), traditional);
            } else {
                form.append(getInput(i, values[i], parent, isArray, traditional));
            }
        });
    };


    redirectTop = redirectTop || false;
    var generatedForm = getForm(url, postDataValues, method, target, traditional);
    $('body', redirectTop ? window.top.document : undefined).append(generatedForm.form);
    generatedForm.submit();
};





/***********************************************************
 *  FORM
 ***********************************************************/
function Html1(){}
Html1.scrollToElement = function (conId){
    let scrollDiv = document.getElementById(conId);
    window.scrollTo(scrollDiv.offsetLeft || 0, scrollDiv.offsetTop || 0);
};
Html1.scrollToElementEnd = function (conId){
    let scrollDiv = document.getElementById(conId);
    scrollDiv.scrollTop = scrollDiv.scrollHeight;
};

/**
 * Search Item List
 $(document).ready(function() {
        Html1.enableSearchFilter('myInput', 'myTable', 'tr');
    });
 * @param inputId
 * @param ulListContainerId
 * @param optionalListItemTagName
 */
Html1.enableSearchFilter = function (inputId, ulListContainerId, optionalListItemTagName){
    $("#" + inputId).on("keyup", function() {
        let value = $(this).val().toLowerCase();
        $("#" + ulListContainerId + " " + (optionalListItemTagName? optionalListItemTagName: 'li') ).filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
};

Html1.toggleDisplayStyle = function (checkBox, selectorOrObject, asInverse){
    asInverse = asInverse? asInverse: false;
    let obj = typeof selectorOrObject === 'object'? selectorOrObject: document.querySelector(selector);
    obj.style.display =  checkBox.checked === asInverse? 'block': 'none';
};

/**
 * @param element
 * @param className
 * @returns {*}
 */
Html1.getClosestElement = function (elem, selector) {
    // Element.matches() polyfill
    if (!Element.prototype.matches) {
        Element.prototype.matches = Element.prototype.matchesSelector || Element.prototype.mozMatchesSelector || Element.prototype.msMatchesSelector || Element.prototype.oMatchesSelector || Element.prototype.webkitMatchesSelector ||
            function(s) {
                var matches = (this.document || this.ownerDocument).querySelectorAll(s), i = matches.length;
                while (--i >= 0 && matches.item(i) !== this) {}
                return i > -1;
            };
    }
    // Get the closest matching element
    for ( ; elem && elem !== document; elem = elem.parentNode ) {
        if ( elem.matches( selector ) ) return elem;
    }
    return null;
};



/**
 * Use Html1.getClosestElement(...) if this failed
 * @param element
 * @param nearestClassName
 * @returns {*}
 */
Html1.getElementByNearestClass = function(element, nearestClassName) { while ((element = element.parentElement) && !element.classList.contains(nearestClassName)); return element; };



/**
 * Convert String to Html
 * @param stringValue
 * @returns {*}
 */
Html1.convertStringToElement = function(stringValue){ return document.createElement("div").innerHTML = stringValue; }

Html1.getElementHtml = (function (obj, deep) {
    if(!obj) return '';
    var elemString, elem = document.createElement("div");
    elem.appendChild(obj.cloneNode(deep));
    elemString = elem.innerHTML;
    elem = null;
    return elemString;
});


/**
 * 'No working yet
 * @type {Function}
 */
Html1.updateElementId = (function(parent){
    var i, cur, numCompare =23;
    var updateId = function (parent) {
        var children = parent.children;
        for(i=0;i<children.length;i++){
            cur=children[i];
            if (cur.nodeType === 1 && cur.id) { cur.id = cur.id+'_'+numCompare; }
            updateId(cur)
        }
    };
    updateId(parent);
});

Html1.removeElementId = (function(htmlContent){
    var value1 = htmlContent.replace(/id="(.*?)"/g, '');
    return value1.replace(/id='(.*?)'/g, '');
    //return htmlContent.replace(/id=/g, '');
});



Html1.getAllImageLink = (function(htmlContent) {
    var doc = document.createElement('div');
    doc.innerHTML=htmlContent;
    var imageLinks = [];
    doc.getElementsByTagName("img").forEach(function(image){ imageLinks.push(image.src);  });
    return imageLinks;
});

Html1.getFirstImageLink = (function(htmlContent){
    //var $regexp = '<img[^>]+src=(?:\"|\')\K(.[^">]+?)(?=\"|\')';
    var doc = document.createElement('div');
    doc.innerHTML=htmlContent;
    return doc.getElementsByTagName("img")[0].src;
});


/**
 *
 * @type {function(*=): *}
 */
Html1.getClonedElement = (function(fieldToCopyId){ return  Html1.convertStringToElement(Html1.removeElementId( Html1.getElementHtml( (document.getElementById(fieldToCopyId)), true)) );  });
/**
 * @type {Function}
 */
Html1.cloneInnerElement = (function(fieldToCopyId, containerToAppendToId, callBack_forWrapprableTag){ var insertHere = document.getElementById(containerToAppendToId);  insertHere.insertAdjacentHTML('beforeend', callBack_forWrapprableTag? callBack_forWrapprableTag(document.getElementById(fieldToCopyId).innerHTML): document.getElementById(fieldToCopyId).innerHTML  ); });
/**
 * // Html1.cloneParentElement(  document.getElementById(fieldToCopyId), document.getElementById(fieldToCopyId).parentNode.className    )
 * @type {Function}
 */
Html1.cloneElement = (function(fieldToCopyId, containerToAppendToId, callBack_forWrapprableTag){  document.getElementById(containerToAppendToId).insertAdjacentHTML('beforeend', callBack_forWrapprableTag? callBack_forWrapprableTag(Html1.getClonedElement(fieldToCopyId)):  Html1.getClonedElement(fieldToCopyId)  ); });

Html1.removeParentElement = (function(obj, nearestClassName){ return Html1.getClosestElement(obj, '.'+nearestClassName).remove(); });

Html1.cloneParentElement = (function(obj, nearestClassName){
    var element = Html1.getElementByNearestClass(obj, nearestClassName).parentNode;
    return element.insertAdjacentHTML('afterEnd', element.innerHTML);
});


Html1.htmlEntities = function(data) { return String(data).replace(/</g, '&lt;').replace(/>/g, '&gt;') };

Html1.sanitizeHTML = function (str) {
    var temp = document.createElement('div');
    temp.textContent = str;
    return temp.innerHTML;
};








/***********************************************************
 *  MATH
 ***********************************************************/
function Math1(){}


/**
 * console.log(format(123456789, '## ## ## ###'));
 = 12 34 56 789
 */
Math1.format = function(value, pattern) {
    var i = 0, v = value.toString();
    return pattern.replace(/#/g, _ => v[i++]);
}
Math1.toMoney = function (amount, currency = "$", decimalCount = 2) {
    if (isNaN(amount) || amount === null) return 0;
    amount = decimalCount <= 0? amount: amount.toFixed(decimalCount);
    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
Math1.toNumber = function(value) { return value.replace(/\D/g, ""); }
Math1.getUniqueId = function(){
    return (Date.now ? Date.now() : +(new Date())) + Math.floor((Math.random() * 1000) + 1);
};
Math1.getRandomNumber = function(){
    return Math.floor((Math.random() * 7) + 1);
};
Math1.getRandomNumber_long = function(){
    return Math.random().toString(36).substr(2, 10);
};
Math1.isNumberKeyPressed = function(evt){
    //<input placeholder="e.g 250000" onkeypress="return isNumberKey(event)" type="text">
    var charCode = ( evt.which ) ? evt.which : event.keyCode
    return !(charCode > 31 && ( charCode < 24 || charCode > 57 ));
};
Math1.toFixed = function(x) {
    //print((22.315).toFixed(2)); //22.31
    //return (Math.floor(this * Math.pow(10, x)) / Math.pow(10, x)) + Math1.toFixed(x);
};
Math1.formatFileSize = function formatFileSize(bytes) {
    var s = ['bytes', 'KB','MB','GB','TB','PB','EB'];
    for(var pos = 0;bytes >= 1000; pos++,bytes /= 1024);
    var d = Math.round(bytes*10);
    return pos ? [parseInt(d/10),".",d%10," ",s[pos]].join('') : bytes + ' bytes';
};

/**
 *  Get The Discount of the price
 */
Math1.discountPrice = function (price, discount) {
    var discountedPrice = price * (100 - discount)
    return Math.round(discountedPrice * 100) / 100;
};






/***********************************************************
 *  URL
 ***********************************************************/
function Url1(){}
Url1.getIpAddressInformation = function(callback){
    $.getJSON("http://smart-ip.net/geoip-json?callback=?",
        function (data) {
            callback(data); //alert(data.countryName); alert(data.city);
        }
    );
};
Url1.getParameter = function(url){ return Url1.parse(url).params; };
/**
 * Url and QueryString Parser.
 * @param {string} url - a Url to parse.
 * @returns {object} an object with the parsed url with the following structure {url: URL, params:{ KEY: VALUE }}
 */
Url1.parse = function (url) {
    if(!url) url = window.location.href;
    url = url.split("#")[0];
    if (url.indexOf('?') === -1) { return {url: url, params: {}}; }
    var parts = url.split('?'), query_string = parts[1], elems = query_string.split('&');
    url = parts[0];
    var i, pair, obj = {};
    for (i = 0; i < elems.length; i += 1) {
        pair = elems[i].split('=');
        obj[pair[0]] = pair[1];
    }
    return {url: url, params: obj};
};
Url1.getCurrentUrl = function (includeParameter) {
    return includeParameter? window.location.href: Url1.parse().url;
};

/**
 * Return if Image Path or defaultImage
 * @param filename
 * @returns {*}
 */
Url1.getFileImagePreview = function(filename, defaultImage){
    let ext = FileManager1.getExtension(filename).toLowerCase();
    switch (ext) {
        case"ico":case"gif":case"jpg":case"jpeg":case"jpc":case"jp2":case"jpx":case"xbm":case"wbmp":case"png":case"bmp":case"tif":case"tiff":case"svg": return filename;
        default: return defaultImage;
    }
};

/**
 * Post Form Data
 * @param link
 * @param formPostData
 * @param target
 * @param traditional
 * @param redirectTop
 */
Url1.sendPost = function(link, formPostData, target, traditional, redirectTop){
    Form1.sendPost(link, formPostData, ((formPostData)? 'POST': 'GET'), target, traditional, redirectTop);
};







// isValidUrl
Url1.isValidURLProtocol = function(url){
    return (url.indexOf("http://") === 0 || url.indexOf("https://") === 0 || url.indexOf("file://") === 0 || url.indexOf("data:") === 0);
};

// encode data
Url1.encodeURIComponentFix = function(str){ return encodeURIComponent(str).replace(/[!'()]/g, escape).replace(/\*/g, "%2A"); };
// redirect website
Url1.redirect = function(url){
    url = (url? url: window.location.href.replace('#', ''));
    if(!url.toLowerCase().startsWith('http')) url = 'http://' + url;
    window.location.href = url;
};





Url1.domainPath = function(){
    return  document.currentScript.src.slice(7)
};
Url1.pingIp = function(ip, callback) {
    if(!this.inUse) {
        this.inUse = true;
        this.callback = callback;
        this.ip = ip;
        var _that = this;
        this.img = new Image();
        this.img.onload = function() {_that.good();};
        this.img.onerror = function() {_that.good();};
        this.start = new Date().getTime();
        this.img.src = "http://" + ip;
        this.timer = setTimeout(function() { _that.bad();}, 1500);
    }
};


/**
 * @param url
 * @param callBack  this.pingServer(pageHost, (status, data)=>{ ... });
 */
Url1.pingServer = function(url, callBack){
    $.ajax({url: url, success: function(result){ callBack(true, result);}, error: function(result){ console.log(':PING ERROR : timeout/error');callBack(false, result);}});
};






/***********************************************************
 *  Picture
 ***********************************************************/
// perform operation through api
function Picture1(){}
//// <input type="file" class="form-control" id="exampleInputFile" onchange="uploadPreview(this, 'imagePreview')">
Picture1.uploadPreview = function(fileUploaderButton, image_id_orNullForClosestImage, defaultImageForFile) {
    var image = image_id_orNullForClosestImage? $('#'+image_id_orNullForClosestImage): $(fileUploaderButton.parentNode).find('img');
    // Import image
    var URL = window.URL || window.webkitURL;
    var blobURL;

    if (URL) {
        var files = fileUploaderButton.files;
        var file;

        if (files && files.length) {
            file = files[0];
            if (/^image\/\w+$/.test(file.type)) {
                blobURL = URL.createObjectURL(file);
                image.attr("src", blobURL);
            } else  image.attr("src", defaultImageForFile);
        }
    } else {
        //image.prop('disabled', true).parent().addClass('disabled');
    }
};



/**
 * JQuery Required
 * Put at the end on page to enable Lazy Load on Page Scroll.
 * make Image with <img class="lazyload" data-src="golden-star.png" alt="Golden Star" />
 * https://bootsnipp.com/snippets/featured/lazy-loading-images-with-placeholders
 */
Picture1.enableLazyLoad = function() {
    window.onload = function() {

        $.fn.isOnScreen = function(){
            var win = $(window);
            var viewport = {
                top : win.scrollTop(),
                left : win.scrollLeft()
            };
            viewport.right = viewport.left + win.width();
            viewport.bottom = viewport.top + win.height();
            var bounds = this.offset();
            bounds.right = bounds.left + this.outerWidth();
            bounds.bottom = bounds.top + this.outerHeight();
            return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom))
        }

        function lazyload() {
            $('.lazyload').each(function() {
                var element = $(this);
                if (element.isOnScreen()) {
                    element.attr('src', element.data('src'));
                    element.removeClass('lazyload');
                }
            })
        }


        lazyload();
        $(window).scroll(function() {
            lazyload();
        })
    }
};







/***********************************************************
 *  File
 ***********************************************************/
// <input type="file" class="form-control" id="exampleInputFile" onchange="readFile(this, function(data){ alert(data) })">
function FileManager1(){}
FileManager1.readFile = function(element, callBack) {
    var reader = new FileReader();
    reader.onload = function (evt) {
        callBack(evt.target.result);
    }
    reader.readAsText(element.files[0]);
};
FileManager1.getFileName = function(path){ return  path.replace(/^.*[\\\/]/, ''); };

/**
 * Get File Extension
 *  or simple return  path.replace(/^.*[.]/, '');
 * @param path
 * @returns {*}
 */
FileManager1.getExtension = function(path){
    var ext = path.split('.').pop();
    return (ext === path)? "": ext;
};







/***********************************************************
 *  Animation
 ***********************************************************/
function Animation1(){}

Animation1.rotateToDegree = function(className, degree) {
    $('.className').animate({  transform: degree }, {
        step: function(now,fx) {
            $(this).css({
                '-webkit-transform':'rotate('+now+'deg)',
                '-moz-transform':'rotate('+now+'deg)',
                'transform':'rotate('+now+'deg)'
            });
        }
    });
};






/***********************************************************
 *  Popup with SwalAlert
 ***********************************************************/
function Popup1(){}
Popup1.confirm = function(title, description, okCallBack){
    if(typeof swal === "undefined") {if(confirm(title + ' (' + Object1.toCleanJsonString(description) + ')')){ return okCallBack() }} else swal({title:title, html:Object1.toCleanJsonString(description), type:'question', showCancelButton: true, confirmButtonText: 'Yes'}).then(function(isConfirm){if(isConfirm && (Object1.getAttribute(isConfirm, 'value', true) === true)) okCallBack() })
};

Popup1.alert = function(title, description, type){
    if(typeof swal === "undefined")  {
        return alert(title + ' (' + Object1.toCleanJsonString(description) + ')');
    } else  {
        return swal({title:title, html: Object1.toCleanJsonString(description), type:type?type:'info'});
    }
};

// Confirm Before Redirecting to Link
Popup1.confirmLink = function(title, description, link){ Popup1.confirm(title, description, function(){ Url1.redirect(link)  }); };

// Confirm Before ending Form Data
Popup1.confirmForm = function(title, description, link, formPostData, target, traditional, redirectTop){ Popup1.confirm(title, description, function(){ Form1.sendPost(link, formPostData, ((formPostData)? 'POST': 'GET'), target, traditional, redirectTop);  }); };

// Confirm Before Clicking The Submit Button Automatically
Popup1.confirmOnSubmitButton = function(title, message, formOnSumitEvent){
    formOnSumitEvent.preventDefault();
    return Popup1.confirm(title, message, function(){
        var element = formOnSumitEvent.target || formOnSumitEvent.srcElement;
        element.submit();
    });
};

Popup1.input = function(title, description, inputAttribute, callback){
    /**
     * inputType : text, email, date, select, textArea, password ...
     */
    swal({
        title: title,
        html: Object1.toCleanJsonString(description),
        input: Object1.getAttribute(inputAttribute, 'type', 'text'),
        inputValue: Object1.getAttribute(inputAttribute, 'value', ''),
        inputPlaceholder: Object1.getAttribute(inputAttribute, 'placeholder', ''),
        showCancelButton: true,
        confirmButtonText: 'Submit'
    }).then(function (value) {
        callback(value);
    }).catch(swal.noop);
};


Popup1.confirmAjax = function(title, description, link, callBack){ Popup1.confirm(title, description, function(){  $.get(link, callBack); }); };

Popup1.confirmOption = function(title, description, optionsKeyValue, callback){
    /**
     *  Pass in PHP SIMILAR ASSOC ARRAY of Object as Select Option
     *   optionsKeyValue = { '1':'ONE', '2':'Two', '5':'FIVE' }
     */
        // convert to html
    var optionHtml = '<div class="form-group"><h4 align="left">' + description + '</h4><select id="categoryName" class="swal2-input form-control">';
    for(var i in optionsKeyValue){
        optionHtml += '<option value="' + i + '">' + optionsKeyValue[i] + '</option>';
    }
    optionHtml += '</select></div>';

    // display popup
    swal({
        title: title,
        html: optionHtml,
        focusConfirm: false,
        confirmButtonText: 'Continue',
        showCancelButton: true,
        preConfirm: function () {
            return new Promise(function (resolve) {
                resolve($('#categoryName').val());
            })
        }
    }).then(function (result) {
        if(result && callback) return callback(result);
    }).catch(swal.noop);
};

Popup1.showLoading = function(title, description){ swal({ title: title, html:Object1.toCleanJsonString(description)/*, type:'info'*/ }); swal.showLoading(); };

Popup1.closeLoading= function(title){ swal({ title: title, type:'success' }); setTimeout(function(){ if(swal.isLoading()) swal.close(); }, 200); };






/***********************************************************
 *  Generic Ajax Call
 ***********************************************************/
// perform operation through api
function Ajax1(){}

/***********************************************************
 *  Generic Ajax Call
 ***********************************************************/
// perform operation through api
function Ajax1(){}
Ajax1.submitForm = function (formId, loadingText, callback, initFunction){
    $(function() {
        formId = "form#" + formId;
        $(formId + " [type='submit']").on("click", function () {
            if (initFunction) if(initFunction() === false) return false;
            if (loadingText) Popup1.showLoading(loadingText, 'Sending Request...');
            var formData = $(formId).serialize();
            //var formDataArray = $(formId).serializeArray();
            var isSuccess = true;
            $(formId + ' [required]').each(function(){
                if( $(this).val() == "" ){
                    isSuccess = false;
                    return Popup1.alert('"' + String1.convertToCamelCase(this.name, ' ') + '" is required');
                }
            });

            if (isSuccess) {
                let rest_url = $(formId).attr('action');
                Ajax1.request(rest_url, formData, function(result){
                    Popup1.closeLoading('completed!');
                    callback(result);
                });
            }
            return false;
        });
    });
};


/**
 *  Call Ajax
 * @param urlMethod ( ajax url)
 * @param loadingContainerId [ put function callback or container_id or null for auto Popup Loader. put "" for nothing ]
 * @param resultCallback
 */
Ajax1.requestGet = function(urlMethod, loadingContainerId, resultCallback){
    if(loadingContainerId == null) loadingContainerId = '';
    if(resultCallback == null) resultCallback = '';
    // loading dialog
    if( typeof loadingContainerId !== 'function' && (loadingContainerId !== '') ){
        if( (loadingContainerId.indexOf(' ') > -1) ){
            var info = ((loadingContainerId.indexOf(' ') > -1)? loadingContainerId: 'Please wait');
            swal({ title: 'Loading...', text: info, onOpen: function () { swal.showLoading(); } });
        }else{
            $('#' + loadingContainerId).html('<span><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Loading... </span>');
        }
    }
    //alert(urlMethod);
    // load all user property
    $.ajax({
        type: 'get',
        url: urlMethod.replace("/ehex-form/", "/ehex-api/"),
        dataType: "json",
        onOpen: loadingContainerId
    }).done(function (data) {
        //console.debug(JSON.stringify(data));

        // stop loading
        swal.close();

        // stop loading dialog
        if((typeof loadingContainerId !== 'function') && (loadingContainerId !== '') && (loadingContainerId.indexOf(' ') < 0))  $('#' + loadingContainerId).html('Completed!');

        // display result
        if(resultCallback === '') Popup1.alert('Done', JSON.stringify(data).replace(/"/g, ""), 'info');
        else resultCallback(data);
    });
};

/**
 * For Ajax Post Request
 * @param url
 * @param postEncodedData
 * @param successCallback
 * @param errorCallback
 * @param cacheables
 * @param requestType
 */
Ajax1.request =  function(url, postEncodedData, successCallback, errorCallback,  cacheables, requestType) {
    $.ajax({
        type: (!requestType  && !postEncodedData?  'get' : 'post'),
        url: url.replace("/ehex-form/", "/ehex-api/"),
        data : postEncodedData,
        dataType: "json",
        restful:true,
        //contentType: 'application/json',
        cache: cacheables || false,
        timeout: 20000,
        async: true,
        beforeSend :function(data) { },
        success:function(data){ successCallback.call(this, data); },
        error: errorCallback? errorCallback: function(data){
            console.debug(data);
            Popup1.alert("Error In Connecting", data);
        }
    });
};


/**
 * Use to load Html to Element Directly
 *  Also Jquery load allow selector in front of url $( "#new-projects" ).load( "/resources/load.html #projects li" );
 * @param url
 * @param toElementId
 * @param postData
 * @param cacheables
 * @param showLoading
 * @param errorCallback
 */
Ajax1.requestHtmlContent = function(url, toElementId, postData, cacheables, showLoading = true, errorCallback){
    if(showLoading) $("#" + toElementId).html("Loading...");
    $.ajax({
        url: url.replace("/ehex-form/", "/ehex-api/"),
        cache: cacheables || true,
        dataType: "html",
        data : postData,
        success: function(data) { $("#" + toElementId).html(data);},
        error: errorCallback? errorCallback: function(data){ console.debug(data); Popup1.alert("Error In Connecting", data); }
    });
};


/**
 * Use to load Text to Element Directly
 * @param url
 * @param $elementId
 */
Ajax1.loadElement = function(url, $elementId){
    Ajax1.requestGet(url.replace("/ehex-form/", "/ehex-api/"), null, function(data){
        document.getElementById($elementId).innerHTML = Object1.toCleanJsonString(data);
    });
};








/**
 * Use to load Option to Select Box Directly
 *  Example {!! HtmlForm1::addSelect('Store Type', ['name'=>"store", 'value'=>StoreType::getKeyValue(), 'onchange'=>'Ajax1.loadElementSelectOption("'.Form1::callApi('StoreCategory::getKeyValue()?_token='.token()).'&store_type_id=" + this.value )' ]) !!}
 * @param $urlToKeyValueJsonArray
 * @param $elementId
 */
Ajax1.loadElementSelectOption = function($urlToKeyValueJsonArray, $elementId){
    Ajax1.requestGet($urlToKeyValueJsonArray, '' , function(data){
        data = Object1.fromJsonString(data);
        $buff = '';
        for(var key in data) $buff += "<option value='" + key + "'>" + data[key] + "</option>";
        document.getElementById($elementId).innerHTML = $buff;
    });
};

/**
 * OnKey Press, Show Search preview like Google search
 * @param url
 * @param searchBoxId
 * @param containerId
 * @param successResult
 */

Ajax1.searchPreview = function(url, searchBoxId, containerId, successResult){
    var searchTimeout;
    var searchReq;
    $("#" + searchBoxId).on('keyup', function() {
        var term = $(this).val();

        if (searchTimeout) clearTimeout(searchTimeout);
        if (searchReq) searchReq.abort();

        searchTimeout = setTimeout(function(){
            if (term.length > 2)  searchController(term).then(function(data) { $("#" + containerId).html(data); if(successResult) successResult(data); });
            else $("#" + containerId).html("");
        }, 500);
    });

    function searchController(term) {
        searchReq = $.ajax({ type : "GET",  url : url, data : { q : term }, async : true, context : this, //timeout : 500
            beforeSend : function() {}, complete : function() {}
        });
        return searchReq;
    }
};




