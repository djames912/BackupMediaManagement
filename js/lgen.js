/* 
 * L-General Version 2.5
 * Changes from Version 1.0 -
 * All code is now compliant with new event handler/object model that I use
 * This means that div ids, object instanse names, and inline event triggers are no longer needed
 * For a copy of the original lgen v1 code please see "status.morinda.com/proxy"
 * 
 * This code is missing some L-General functions/classes(still being converted from version 1.0)
 *
 * 
 * Author: Logan Barnes, March 2012,
 * Morinda, Inc
 * 
 */


//Arrays for specifying events based on an index
moz_events = ['click','keyup','mouseover','mouseout'];
ei_events = ['onclick','onkeyup','onmouseover', 'onmouseout'];

//ENUM
events = {'click':0,'keyup':1,'m_over':2,'m_out':3};

//Code for automatically detecting and setting event handler type.
function add_event(element, event, func) {
    if(document.addEventListener){ //code for Moz
       // //console.log('here');
    	element.addEventListener(moz_events[event],func,false);
    }
    else{
	element.attachEvent(ei_events[event],func); //code for IE
    }
}


//New Streamlined getData function (Basis was reloadData from status)
// This now takes a ptr to a function as a parameter for callback
function getData(json_string, call_back, async) {
    var	url = 'includes/ajax.php';
    var params = 'func='+json_string;
    var request = new XMLHttpRequest();
    var called =0;
    
    request.onreadystatechange = function () {	
        if(request.readyState == 4) {
           var jObj = JSON.parse(request.responseText);
           if(!async)
               return jObj;
            call_back(jObj);
        }
    };
    request.open('POST', url, async);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.send(params);
}

//Checks if a value is an Array
function is_array(t_val) {
	if(t_val instanceof Array) {
		return true;
	}
	return false;
}

//Function for turning a string into stars 
function strmod(str, num) {
    var tmp =''; 
    for(var i =0; i < (str.length - num); i++ ) {
        tmp += '*';
    }
    tmp += str.substr(str.length - num, str.length-1);
    return tmp;
}

// GENERAL FUNCTION FOR PARSING CREDIT CARD INFORMATION
// COULD POSSIBLY BE USED IN CC_INFO CLASS
function cc_parse(str) {
    str = str.replace(/\s{2,}/g, '');
    var cc_obj = new Object();
    cc_obj.exp = new Object();
    var sname;
    var tmp;

    if(str.match(/%E/) != null || str.match(/E\?/) != null|| str.match(/;E/)!= null) {
          //console.log('ERROR');
          return 0;
    }
    
    if((tmp = str.match(/;([0-9]*)\=/)))
        cc_obj.cc_num = tmp[1];
    else
        return 0;
    
    var name = str.match(/\^(.*)\^/);
    //console.log('THIS IS THE NAME: '+name);
    if(name == null)
        return 0;
    if(name[1].match(/\//)) {
        sname = name[1].split('/');
        cc_obj.first_name = sname[1];
        cc_obj.last_name = sname[0];
        //console.log('contains slash');
    }
    else if((sname = name[1].split(' '))) {
        //console.log('contains space ');
         cc_obj.first_name = sname[0];
        cc_obj.last_name = sname[1];
    } 
    tmp = str.match(/\=([0-9]*)\?/);
    cc_obj.exp.year = tmp[1].substring(0,2);
    cc_obj.exp.month = tmp[1].substring(2,4);
    //console.log('PARSE DONE');
    
    return cc_obj;
}

function loading_text(root,text) {
    var me = this;
    this.root = root;
    this.text = text;
    this.max_dots = 6;
    this.c_dots = 0;
    this.state = 0;
    this.t_val;
    
    this.animate = function() {
        if(me.state == 1) {
            var dots = '';
            for(i=0; i < me.max_dots; i++) {
                dots += '. ';
                if((i-1) == me.c_dots) {
                    me.c_dots = i;
                    break;
                }
            }
            if(me.c_dots >= me.max_dots-1) {
                me.c_dots =0;
                dots = '. ';
            }
            me.root.innerHTML = me.text + dots; 
            me.t_val = setTimeout(function(){me.animate();}, 500)}
    }
    this.start = function () {
        me.state = 1;
        me.animate();
    }
    this.stop = function () {
        clearTimeout(me.t_val);
        me.state = 0;
    }
}

function timer (max_time, update) {
	var me = this;
	this.state = null;
	this.clock = new Object();
	this.start_date;
	this.total_time = parseInt(max_time);
	this.orig_time = parseInt(max_time);
	this.time_passed = 0; //Seconds
        this.hook = null;
        me.update = update;
	
	this.start = function () {
		if(me.state != 0) {
			me.state = 0;
	 		me.start_date = new Date();
			me.time_logic();
		}
	}
	this.stop = function () {
		me.total_time = me.total_time - me.time_passed;
		//me.btn_start.setState(false);
		clearTimeout(me.delay);
		me.state = 1;
	}
	this.reset = function() {
		me.stop();
		me.total_time = me.orig_time;
		me.time_passed = 0;
              //  me.start();
	}
	this.set_time = function(time) {
		me.stop();
		me.time_passed = 0; 
		me.total_time = parseInt(time);
		me.orig_time = parseInt(time);
	}
	this.time_logic = function() {
		if(me.state== 0 && me.time_passed < me.total_time) {		
			////console.log('entered time_logic');
			var date = new Date();
			var delay = function () {me.time_logic()};
			this.time_passed = Math.round((date.getTime() - me.start_date.getTime())/1000);
                       if(me.hook != null) {
                           me.hook();
                       }
			me.delay = setTimeout(delay, me.update);
		}
		else {
			me.stop();
		}
	}
}

//548 (TWO MOVES)
function margin_controls(inc, div_to_control, placement_div) {
    var me = this;
    this.root = placement_div;
    this.inc = inc;
    this.mtracker = 0;
    this.marginL = 1024;
    this.cdiv = div_to_control;
    this.ctrl_divs = new Object();
    this.ctrl_divs.left = document.createElement('div');
    this.ctrl_divs.left.setAttribute('class','tap_left');
    this.ctrl_divs.left.value = 'left';
    this.ctrl_divs.left.innerHTML = '&lt';
    this.ctrl_divs.right = document.createElement('div');
    this.ctrl_divs.right.value = 'right';
    this.ctrl_divs.right.innerHTML = '&gt';
    this.ctrl_divs.right.setAttribute('class','tap_right');
    this.root.appendChild(this.ctrl_divs.left);
    this.root.appendChild(this.ctrl_divs.right);
    
    this.call_back = function() {
        var dist = me.inc;
        if(this.value == 'right')
            dist = me.inc * -1;
        
        if((this.value == 'left' && me.mtracker < 0) || (this.value=='right'&& me.mtracker > me.marginL))
            me.mtracker +=  dist;
            //s_div.style.marginLeft =  mtracker+'px' ;
            $(me.cdiv).animate({"margin-left":me.mtracker});
    }
    this.reset = function() {
        me.mtracker = 0;
        $(me.cdiv).animate({"margin-left":me.mtracker});
    }
    add_event(this.ctrl_divs.left, 0, this.call_back);
    add_event(this.ctrl_divs.right, 0, this.call_back);
}

/*Array.prototype.contains = function(obj, rtype) {
    for(var key in this) {
        if (this[key] === obj) {
            if(rtype)
                return key;
            return true;
        }
    }
    if(rtype)
        return -1;
    return false;
}*/

function contains(arr,obj, rtype) {
     for(var key in arr) {
        if (arr[key] === obj) {
            if(rtype)
                return key;
            return true;
        }
    }
    if(rtype)
        return -1;
    return false;
}


function getOffset( el ) {
    var _x = 0;
    var _y = 0;
    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
        _x += el.offsetLeft - el.scrollLeft;
        _y += el.offsetTop - el.scrollTop;
        el = el.offsetParent;
    }
    return {top: _y, left: _x};
}



//Returns todays Timestamp
function today() {
	var tmp = new Date();
	return parseInt(tmp.getTime()/1000);
}

// Return correclty formatted Unix time based on time string 
// example: 9/15/2011 -> unix timestamp
function text_to_time(stime) {
	var mDate = new Date(stime);
	var myEpoch = mDate.getTime()/1000.0;
	return myEpoch
}


function time_to_text(time) {
    var tmp = new Date(time*1000)
    var str = (tmp.getMonth()+1)+'/'+tmp.getDate()+'/'+tmp.getFullYear();
    return str;
}



function selMenu(data) {
    var nsel = document.createElement('select');
    for(var key in data) {
        if(key == 'contains')
                continue;
        var tmp_opt = document.createElement('option');
        tmp_opt.value = data[key].id;
        tmp_opt.innerHTML = data[key].label;
        nsel.appendChild(tmp_opt);
    }
    return nsel;
}

function tInput() {
    var tmp = document.createElement('input'); 
    tmp.type = 'text';
    return tmp;
}

function lf_pair(root, label, element) {
    //var spn = document.createElement('span');
    //spn.setAttribute('class','spn');
    var spn = document.createElement('label');
    spn.setAttribute('class','spn');
    spn.innerHTML = label;
    element.setAttribute('class', 'iput');
    var struct = document.createElement('div');
    struct.setAttribute("class", "div_tab");
    struct.appendChild(spn);struct.appendChild(element);
    root.appendChild(struct);
}

//THIS PROTOTYPE IS LGEN 2.0 APPROVED (NEW EVENT HANDLING STYLE)
//---------------------------------------------------------------------------
// prototype/Object for Generating a menu and linking menu items to page divs
// This will give the menu context and control over divs
// cont = name of div you want menu to be contained in
// args = [object,object]Need to fix this input method
// Oname = put the name of the object you created there, will help 
//-------------------------------------------------------------------------- 
function menu(cont, args) {
	var me = this;
	this.marker = 'mMark';
	this.cont_div= document.getElementById(cont);
	this.mObj = [];
	this.display = 3;
	
	this.animate = function(hide, show) {
		pub_arg = show;	
		$(me.mObj[hide].page_div).hide("fade", {}, 300, function (){$(me.mObj[show].page_div).show("fade", {  }, 300)});
	}
	this.onclick = function () {
		var index = this.index;
		for(var i = 0; i < me.mObj.length; i++) {
			if(me.mObj[i].disp == 1) {
				if(i == index) {
					return;
				}
				me.mObj[i].disp = 0; 
				//this.mObj[i].page_div.style.display = 'none';
				//this.mObj[i].page_div.style.display = 'block';	
				//this.mObj[i].page_div.style.display = 'none';
				me.mObj[i].menu_div.setAttribute("class", "mbut");

				me.mObj[index].disp = 1;
				//this.mObj[index].page_div.style.display = 'block';	
				me.mObj[index].menu_div.setAttribute("class", "msel");
				me.mObj[index].tmp = me.mObj[index].menu_div.getAttribute('class');
				
				if(i < index) {
					hide_dir = 'left';
					show_dir = 'right';
				}
				else {
					hide_dir = 'right';
					show_dir = 'left';
				}
				me.animate(i, index);
				return;		
			}
		}		
	}
	this.onhoover = function () {
		var index = this.index;
		me.mObj[index].tmp = me.mObj[index].menu_div.getAttribute('class');
		me.mObj[index].menu_div.setAttribute("class", "mhov");
	}
	this.mout =function () {
		var index = this.index;		
		me.mObj[index].menu_div.setAttribute("class", me.mObj[index].tmp);
	}

	for(var i = 0; i < args.length; i++) {
		this.mObj[i] = new Object;
		this.mObj[i].mname = args[i].mname;
		this.mObj[i].page_div = document.getElementById(args[i].divName);
		this.mObj[i].menu_div = document.createElement('div');
		this.mObj[i].menu_div.setAttribute("class", "mbut");
		this.mObj[i].menu_div.id = args[i].mname+this.marker;
		this.mObj[i].menu_div.innerHTML = this.mObj[i].mname;
		this.mObj[i].menu_div.index = i;
		add_event(this.mObj[i].menu_div, events.click, this.onclick); 
		add_event(this.mObj[i].menu_div, events.m_over, this.onhoover); 
		add_event(this.mObj[i].menu_div, events.m_out, this.mout); 
		this.cont_div.appendChild(this.mObj[i].menu_div);	
		if(i == this.display) {
			this.mObj[i].disp = 1;
			this.mObj[i].menu_div.setAttribute("class", "msel");
		}
		else {
			this.mObj[i].disp = 0;
			this.mObj[i].page_div.style.display = 'none';
			this.mObj[i].menu_div.setAttribute("class", "mbut");
		}
	}

	
}

function drop(wrapper, tbox, call_back) {
    var me = this;
	this.state = false;
	this.tbox = tbox;
	this.selected = -1;
	this.wrap = wrapper;
	this.drop_div;
	this.srch;
	this.sel_v = null;
	this.blk = false;
    this.hook = call_back;
	
	this.init = function() {
		this.drop_div = document.createElement('div');
		this.drop_div.setAttribute('class','rbox');
        add_event(this.tbox, events.click, this.focus);
        add_event(this.drop_div,events.m_over, this.setBlk_true);
        add_event(this.drop_div,events.m_out, this.setBlk_false);
		var pos = getOffset(this.tbox);
		this.drop_div.style.left = pos.left+'px';
		this.drop_div.style.top = (pos.top+20)+'px';
		this.wrap.appendChild(this.drop_div);
        this.drop_div.style.display = 'none';
		//this.state = true;
	}
    this.setPos = function() {
        var pos = getOffset(me.tbox);
		me.drop_div.style.left = pos.left+'px';
		me.drop_div.style.top = (pos.top+18)+'px';
    }
	this.reset = function() {
		me.tbox.value = '';
		me.ufocus();
	}
	this.mouse = function () {
		if(me.sel_v == null){
			this.style.backgroundColor = 'grey';
			me.sel_v = this;
		}
		else {
			this.style.backgroundColor = 'grey';
			me.sel_v.style.backgroundColor = 'transparent';
			me.sel_v = this;
		}
	}
	this.mclick = function () {
        console.log(this);
        me.hook(this.name);
		me.blk=0;
		me.reset();
	}
	this.focus = function() {
         me.setPos();
		if(me.drop_div != null && !(me.state)) {			
			$(me.drop_div).show('blind', 200);
			me.state = 1;
		}
	}
    this.ufocus = function() {
        if(me.drop_div != null && me.state && !(me.blk)) {
				$(me.drop_div).hide('blind', 200);
				me.state =0;
		}
    }
	this.setBlk_false = function () {
			me.blk = false;		
	}
    this.setBlk_true = function() {
        me.blk = true;
    }
	this.update_list = function(data) {
        me.setPos();
        me.drop_div.innerHTML = '';
		me.selected = -1;
		if(typeof data.err != "undefined") {
			me.drop_div.innerHTML = data.err;
            return;
		}		
		else if(data != null && data != "") {		
			for(i =0; i < data.length; i++) {
				this.list_len = i;
                var tmp_li = document.createElement('li');
				tmp_li.name = data[i].label;
                tmp_li.innerHTML = data[i].label;
                add_event(tmp_li, events.click, me.mclick);
                add_event(tmp_li, events.m_over, me.mouse);
                me.drop_div.appendChild(tmp_li);
                if(i >= 9) {
					break;
				}		
			}
		}	
	}
      this.init();
}

function button (cont_div, button_text, hook) {
	var me = this;
        this.wrap;
	this.icon = new Object();
	this.button;
	this.name = button_text;
	this.cl = 0;
        this.hook = hook;

        this.fail = function() {
           // if(me.cl >= 1) 
                me.icon.image.setAttribute('class', 'xmark');
            return;	
        }
        this.success = function() {
                // if(me.cl >= 1)
                    me.icon.image.setAttribute('class', 'cmark');
		return;
        }
	this.click = function() {
                me.hook();
		//me.cl++;	
	}
        this.ldr = function () {
            me.icon.image.setAttribute('class', 'loadg');
        }
	this.hov = function () {
		me.button.style.backgroundColor = "lightgray";
		me.icon.wrap.style.backgroundColor = "lightgray";
	}
	this.out = function () {
		me.button.style.backgroundColor = "white";
		me.icon.wrap.style.backgroundColor = "white";
	}
	this.init = function() {
		this.wrap = document.createElement('div');
		this.icon.wrap = document.createElement('div');
		this.icon.image = document.createElement('div');
		this.button = document.createElement('div');
		this.wrap.setAttribute('class','bwrap');
		this.icon.wrap.setAttribute('class','i_con');
		this.icon.image.setAttribute('class','bin');
		this.button.setAttribute('class','button1');
		this.icon.wrap.appendChild(this.icon.image);
		this.wrap.appendChild(this.icon.wrap);
		this.wrap.appendChild(this.button);
		cont_div.appendChild(this.wrap);
		this.button.innerHTML = button_text;
                add_event(this.button, events.click, this.click);
                add_event(this.button, events.m_over, this.hov);
                add_event(this.button, events.m_out, this.out);
                add_event(this.button, events.m_over, function() {this.style.cursor="pointer";});
	}
	this.init();
}

function css_table(tdef, root) {
   var cont = document.createElement('div');
   var head = document.createElement('div');
   var row_cont = document.createElement('div');
   if(tdef.dim.root != null) {
       cont.style.width = tdef.dim.root.width;
       cont.style.height = tdef.dim.root.height;
       root.appendChild(cont);
        
   }
   else
       cont = root;
   
   cont.setAttribute('class','c_cont');
   cont.appendChild(head);
   cont.appendChild(row_cont);
   
  
   head.setAttribute('class','c_head');
   row_cont.setAttribute('class','c_rcont');
   head.style.color = 'white';
   head.style.fontWeight = 'bolder';
   
  for(var key in tdef.head) {
    
            var col = document.createElement('div');
            if(key < tdef.head.length-1 )
                col.setAttribute('class','c_col c_col_mid');
            else
                col.setAttribute('class','c_col');
            
            col.style.fontSize="15px";
            if(tdef.dim.col[key] != null)
               var tmp1 = tdef.dim.col[key]+'%';
            else
                var tmp1= ((cont.offsetWidth/tdef.head.length)/cont.offsetWidth)*99+'%';
            col.style.width = tmp1
            //console.log(tmp1);
            if(is_array(tdef.head[key]))
                col.appendChild(tdef.head[key][0]);
            else 
                col.innerHTML = tdef.head[key];
            head.appendChild(col);  
   }
    var row = document.createElement('div');    
    for (var key1 in tdef.rows){
         row = document.createElement('div');
        row.setAttribute('class','c_row');
        row_cont.appendChild(row);
        if(key1%2)
            row.style.backgroundColor='#EEE';
        else
            row.style.backgroundColor="#DDD";
        var cnt = 0;
        for(var key2 in tdef.rows[key1]) {
    
            var col = document.createElement('div');
            //if(cnt < tdef.rows[key1].length-1 )
                col.setAttribute('class','c_col c_col_mid');
            //else
              //  col.setAttribute('class','c_col');
             
            if(tdef.dim.col[cnt] != null)
                var tmp1 = tdef.dim.col[cnt]+'%';
            else
                var tmp1= ((cont.offsetWidth/tdef.rows[key1].length)/cont.offsetWidth)*99+'%';
            col.style.width = tmp1
           // console.log(tmp1);
            col.innerHTML = tdef.rows[key1][key2];
            row.appendChild(col);
            cnt++;
        }
    } 
    head.style.width = row.offsetWidth+'px';
   
}