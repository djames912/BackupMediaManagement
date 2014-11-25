//# - REQUIRES lgen.js version 2.5
uname = 'Unknown';
function t_callback(d) {
    //console.log(d);
}

function init(u) {
    //console.log(u);
    uname = u;
    //console.log("# ---- init");
    new_tape = new add_tape(document.getElementById('add'));
    new_tape.init();
    //var menu_args = [{"mname":"Modify Batch","divName":"mod_batch"},{"mname":"Create Batch","divName":"batch"},{"mname":"Reports","divName":"stats"},{"mname":"Add Tape","divName":"add"},{"mname":"Modify Tape","divName":"mod"},{"mname":"Admin","divName":"admin"}];
    //new menu('menu', menu_args);
    new basic_report(document.getElementById('stats'));
    new mod_tape(document.getElementById('mod'));
    new create_batch(document.getElementById('batch'));
    new mod_batch(document.getElementById('mod_batch'));
}

current = null;

function loc_log(tmp) {
    //console.log(tmp);
    var pos = getOffset( tmp );
   /* //console.log(pos);
    //console.log(tmp.offsetLeft);
    //console.log(tmp.offsetTop);*/
    var sel = document.getElementById('sel');
    sel.style.display = 'block';
    
    var func = function() {
        tmp.style.backgroundColor = 'green';
        sel.style.display = 'none';
        
    }
    if(current != null) {
        current.style.backgroundColor='blue';
        current = tmp;
    }
    else {
        current = tmp;
    }
    
    $(sel).animate({"left":pos.left, "top":(pos.top-5)}, func);   
}

function tape_scan() {
}


function display_obj(cont_div) {
    var me = this;
    this.root  = cont_div
    this.form_div = document.createElement('div');
    this.form_div.setAttribute('class','form1');
    this.results_div = document.createElement('div');
    this.results_div.setAttribute('class','results');
    this.root.appendChild(this.form_div);
    this.root.appendChild(this.results_div);
    
    this.updateLeft = function(element) {
        this.form_div.appendChild(element);
    }
    this.updateRight = function (element) {
        this.results_div.appendChild(element);
    }
    this.clearLeft = function() {
        me.form_div.innerHTML = '';
    }
    this.clearRight = function() {
        me.results_div.innerHTML = '';
    }
    this.clearAll = function() {
        me.clearLeft();
        me.clearRight();
    } 
}

function create_batch(cont_div) {
    var me = this;
    this.root = cont_div
    this.display = new display_obj(this.root);
    this.form = new Object();
    
    this.init = function() {
        this.form.ora = selMenu({'one':{'label':'No','id':'0'},'two':{'label':'Yes','id':'1'}});
        lf_pair(this.display.form_div, "Is Oracle ?", me.form.ora);
        
        this.form.date = tInput();
        lf_pair(me.display.form_div, "Date", me.form.date);
        ui_date(this.form.date);
        
        
        this.form.tape_id = new tape_capture(this.input, this.display.results_div,this.display.form_div);
       
       this.form.submit = button('Submit');
       this.display.form_div.appendChild(this.form.submit);
       btn(this.form.submit, me.submit); 
       $(this.form.submit).addClass('green-button');
       $(this.form.submit).button();
      
    }
    
    this.input = function() {
        getData(JSON.stringify({'args':me.form.tape_id.tid, 'func':'tapeExists'}), me.form.tape_id.set_status, true);
    }
    this.submit_callback = function(data) {
        if(data.ERR) {
            me.display.form_div.innerHTML += '<div background-color:red; color:white;>'+data.ERR+'</div>';
        }
        else {
            $(me.display.results_div).hide("slide", { direction: "down"}, 600, function (){ me.display.results_div.innerHTML = '<div style="background-color:green; color:white;">Batch Creation Successfull!</div>'; $(me.display.results_div).show("slide", { direction: "down"}, 600)});
        }
    }
    this.submit= function() {
        if(me.form.tape_id.group_status()) {
            var args = {'bcodes':me.form.tape_id.scanned.id,'uname':uname,'ora':me.form.ora.value,'date':me.form.date.value};
            getData(JSON.stringify({'args':args, 'func':'addTapeBatch'}), me.submit_callback, true);
        }
        else {
         me.display.form_div.innerHTML += '<div background-color:red; color:white;>Can\'t add incomplete Batch</div>';
        }
    }
    this.init();
}

function mod_batch(cont_div) {
    var me = this;
    this.root = cont_div
    this.display = new display_obj(this.root);
    this.form = new Object();
    this.batches = new Object();
    this.context = null;
    this.init = function() {
        getData(JSON.stringify({'args':{}, 'func':'getAll'}), this.finit, true);
    }
    this.finit = function(data) {
        //console.log(data);
        me.form.date = tInput();
        lf_pair(me.display.form_div, "Date", me.form.date);
        ui_date(me.form.date);
         me.form.loc_id = selMenu(data.loc);
        lf_pair(me.display.form_div, "Locations", me.form.loc_id);
        me.form.tape_id = new tape_capture(me.input, me.display.results_div,me.display.form_div);    
        me.form.submit = button('Submit');
        me.display.form_div.appendChild(me.form.submit);
        btn(me.form.submit, me.submit); 
       $(me.form.submit).addClass('green-button');
       $(me.form.submit).button();
       $(me.form.submit).attr("disabled", true);
         getData(JSON.stringify({'args':'', 'func':'get_rtn_batch'}), me.blist, true);
    }
    this.blist = function(data) {
        
           me.batches = data;
        var tmp_div = document.createElement('div');
        tmp_div.setAttribute('class','batch');
        
        for(var key in data) {
            var tmp_btn = button(data[key].label);
             tmp_div.appendChild(tmp_btn);
             tmp_btn.value = key;
             btn(tmp_btn, me.batch_sel);
             $(tmp_btn).addClass('green-button');
             $(tmp_btn).button();
        }
        me.display.form_div.appendChild(tmp_div);
    }
    this.batch_sel = function () {
        me.form.tape_id.clear();
        for(var i =0; i < me.batches[this.value].tapes.length; i++)
            me.form.tape_id.add(me.batches[this.value].tapes[i].tape_id);
        me.form.tape_id.display_list();
        me.context= this.value;
    }
    this.input = function() {
        me.form.tape_id.display_list();
        if(!(contains(me.form.tape_id.scanned.status,0))) {
             $(me.form.submit).attr("disabled", false);
        }
    }
    this.submit_callback = function(data) {
        if(data.ERR) {
            me.display.form_div.innerHTML += '<div background-color:red; color:white;>'+data.ERR+'</div>';
        }
        else {
            $(me.display.results_div).hide("slide", { direction: "down"}, 600, function (){ me.display.results_div.innerHTML = '<div style="background-color:green; color:white;">Batch Checked in!</div>'; $(me.display.results_div).show("slide", { direction: "down"}, 600)});
        }
    }
    this.submit= function() {
        if(me.form.tape_id.group_status()) {
            var args = {'tapes':me.batches[me.context].tapes,'uname':uname,'bid':me.context,'date':me.form.date.value, 'loc_id':me.form.loc_id.value};
            getData(JSON.stringify({'args':args, 'func':'mod_batch'}), me.submit_callback, true);
        }
        else {
         me.display.form_div.innerHTML += '<div background-color:red; color:white;>Can\'t add incomplete Batch</div>';
        }
    }
    this.init();
}


function mod_tape(cont_div) {
   var me = this;
   this.root = cont_div;
   this.tape_id;
   this.input_box = document.createElement('input');
   this.input_box.type = 'text';
   this.display = new display_obj(this.root);
   this.display.updateLeft(this.input_box);
   
   this.init = function() {
       add_event(this.input_box, events.keyup, this.input);
   }
    this.init();
}

//STATIC CLASS FOR INITAILIZING THE ADD TAPE INTERFACE AND CODE
function add_tape(cont_div) {
    var me = this;
    this.root = cont_div;
    this.fields = {'tape_id':'','date':'','ven_id':'','loc_id':'','mtype':'','po_num':''};
    this.values =  {'tape_id':'','date':'','ven_id':'','loc_id':'','mtype':'','po_num':'', 'uname':uname};
    this.form_div;
    this.results_div ;
    this.tape_filter;
    
    this.final_init = function(data) {
        me.fields.ven_id = selMenu(data.vendors);
        lf_pair(me.form_div, "Vendors", me.fields.ven_id);
        me.fields.loc_id = selMenu(data.loc);
        lf_pair(me.form_div, "Locations", me.fields.loc_id);
        me.fields.mtype = selMenu(data.mtype);
        lf_pair(me.form_div, "Media Type", me.fields.mtype);
        me.fields.date = tInput();
        lf_pair(me.form_div, "Date", me.fields.date);
        me.fields.po_num = tInput();
        me.fields.po_num.value = '11111';
        lf_pair(me.form_div, "PO#", me.fields.po_num);
        me.tape_filter = new tape_capture(me.input_hook,me.results_div,me.form_div);
        ui_date(me.fields.date);
    }
    this.init = function() {
        this.form_div = document.createElement('div');
        this.form_div.setAttribute('class','form1');
        this.results_div = document.createElement('div');
        this.results_div.setAttribute('class','results');
        this.root.appendChild(this.form_div);
        this.root.appendChild(this.results_div);
        getData(JSON.stringify({'args':{}, 'func':'getAll'}), this.final_init, true);
    }
    this.updateValues = function() {
        for(var key in me.values) {
            if(me.fields[key]) {
                me.values[key] = me.fields[key].value;
                //console.log("val: "+me.values[key]);
            }
        }  
    }
    this.submit = function () {
        getData(JSON.stringify({'args':me.values, 'func':'addNewTape'}), me.tape_filter.set_status, true);
    }
    this.input_hook = function() {
        me.updateValues();
         me.values.tape_id = me.tape_filter.tid;
         me.tape_filter.display_list();
         me.submit();
    }
}


function basic_report (root) {
    var me = this;
    this.wrap = root;
    this.results;
    this.tlist_args = {'args':'', 'func':'getTapes'};
    this.tdata_args = {'args':'', 'func':'lookUpTape'};
    this.fields = {'tape_id':'','fdate':'','tdate':'','loc_id':''};
    this.input = document.createElement('div');
    this.form;
    this.tape_id;
    this.history = document.createElement('div');
    this.history.style.color = 'white';
    //add_event(tmp_item, events.click, e_callback)
    this.wrap.appendChild(this.input);
    this.wrap.appendChild(this.history);
    //this.input.appendChild(this.tape_id);
    this.post_init = function(data) {
       //me.fields.tape_id =tInput();
       //me.fields.fdate = tInput();
       //me.fields.tdate = tInput();
        lf_pair(me.input, "Tape ID ", me.fields.tape_id);
        
       //test
       /* lf_pair(me.input, "From Date", me.fields.fdate);
        lf_pair(me.input, "To Date", me.fields.tdate);
        
        data.loc.unshift({'id':'null','label':'Any'});
        me.fields.loc_id = selMenu(data.loc);
        lf_pair(me.input, "location", me.fields.loc_id);
        
        ui_date(me.fields.fdate);
        ui_date(me.fields.tdate);
        
        var button = document.createElement('button');
        button.value = 'submit';
        button.innerHTML = 'Submit';
        me.input.appendChild(button);
        btn(button, me.run_report);*/
        
        me.input.style.width = '100%';
        me.input.style.height = '35%';
        me.history.style.width = '100%';
        me.history.style.height = '65%';
        me.history.style.backgroundColor = 'green';
        //me.input.style.backgroundColor = 'blue';
    }
     
    this.init = function() {
       me.fields.tape_id =tInput();
          this.drop = new drop(this.input, me.fields.tape_id , this.rtn_data);
          add_event(me.fields.tape_id, events.keyup, this.capt_input);
           getData(JSON.stringify({'args':{}, 'func':'getAll'}), this.post_init, true);
    }
    this.tape_detail_callback = function(data) {
        //console.log(data);
        me.history.innerHTML = '';
        var tdef = {'head':['ID','Date','Tape Id','Location', 'User','Batch','Number in Batch'],'dim':{'root':null,col:[7,15,15,15,15,14,14]},'rows':[]};
        
        for(var i = data.tapeHistory.length-1; i >= 0; i--) {
            tdef.rows.push(data.tapeHistory[i]);
        }
        css_table(tdef, me.history);
        
        /*var tmp_head = document.createElement('div');
        tmp_head.innerHTML = '<span>Tape ID: '+data.tapeLabel+'</span><span> Tape Type: '+data.tapeType+'</span><span>Vendor: '+data.vendor+'</span>';
        me.history.appendChild(tmp_head);
        var tmp_table = document.createElement('table');
        tmp_table.style.color="white";
        for(var i =0; i < data.tapeHistory.length; i++) {
            var tr = document.createElement('tr');
            for(var key in data.tapeHistory[i]) {
                var td = document.createElement('td');
                td.innerHTML = data.tapeHistory[i][key];
                tr.appendChild(td);
            }
            tmp_table.appendChild(tr);
        }
        me.history.appendChild(tmp_table);*/
    }
    this.rtn_data = function(data) {
        //console.log("RTN DATA: "+data);
        me.tdata_args.args = data;
         getData(JSON.stringify(me.tdata_args), me.tape_detail_callback, true);
    }
    this.server_callback = function(data) {
        if(!( data == null || data.err)) {
            me.drop.update_list(data);
        }
        else {
            me.drop.update_list([{'label':'No Matches Found'}]);
        }
        
    }
    this.run_report = function() {
        var nval = new Array();
        for(var key in me.fields) {
            //console.log(key+" "+me.fields[key].value);
            nval[key] = me.fields[key].value;
        }
        
        var tmp= {'args':nval, 'func':'report'};
        getData(JSON.stringify(tmp),me.print_res,true);
        //console.log('HERE');
    }
    this.print_res = function (data) {
        //console.log("here2");
        //console.log(data);
    }
    this.capt_input = function() {
        if(this.value != null && this.value != '') {
            me.tlist_args.args = this.value;
            getData(JSON.stringify(me.tlist_args), me.server_callback, true);
        }
    }
    
    this.init();
   
}

function ui_date(cont_div) {
    $(function() {
        $(cont_div).datepicker();
        $(cont_div).datepicker("setDate", time_to_text(today()));
    });	
}

//FUNCTION FOR BUILDING A JQUERY BUTTON
function btn(fbtn_div, func) {
    $(function() {
        $( fbtn_div )
        .button()
        .click(func)
        });
}

function button(value) {
    var tmp = document.createElement('button');
    tmp.value = value;
    tmp.innerHTML= value;
    
    return tmp;
}

function input(type) {
 var tmp;
    if(type=='button')
        tmp = document.createElement('button');
    else {
        tmp = document.createElement('input');
        tmp.type = '';
    }
    return tmp;
}


function tape_capture(hook, op, cont) {
    var me = this;
    this.scanned = new Object();
    this.scanned.id = new Array();
    this.scanned.status = new Array();
    this.text = tInput();
    this.hooks = hook;
    this.output = op;
    this.root = cont;
    this.tid  = '';
    
    this.init = function() {
        add_event(me.text, events.keyup, me.input)
        lf_pair(this.root, "Tape Id", me.text);
        
        
    }
    /*this.chk_hooks = function() {
        if(me.hooks.match)
            return true;
        //console.err('NO HOOKS PROVIDED!');
        return false; 
    }*/
    this.input = function () {
        var str = this.value;
        var tmp = this;
        var match = '';
        var key ='';
        if((match = str.match(/([0-9]{5,6}L[0-9])/))) {
            if(!(contains(me.scanned.id, match[0]))) {
                this.style.backgroundColor ='green';
                this.style.color = 'white';
                setTimeout(function() {tmp.style.backgroundColor ='white';tmp.style.color ='black'}, 500);
                me.scanned.id.push(match[0]);
                me.scanned.status.push(0);
                me.tid = this.value;
                this.value = '';
                me.hooks();
            }
            else if((key = contains(me.scanned.id,match[0],true))!= -1 && me.scanned.status[key] == 0) {
                me.scanned.status[key]=1;
                me.tid = this.value;
                this.value = '';
                me.hooks();
            }
        }
        else if(str.length > 7){
            this.style.backgroundColor ='red';
            this.style.color ='white';
            setTimeout(function() {tmp.style.backgroundColor ='white';tmp.style.color="black";tmp.value='';}, 2000);
        }
       
    }
    this.add = function (tape_id) {
        me.scanned.id.push(tape_id);
        me.scanned.status.push(0);
    }
    this.clear = function() {
         me.scanned.id = new Array();
         me.scanned.status = new Array();
    }
    this.display_list = function() {
        me.output.innerHTML = '';
        /*for(var key in me.scanned.id) {
            if(key == 'contains')
                continue;
            var tmp = document.createElement('li');
            tmp.innerHTML = me.scanned.id[key];
            switch(me.scanned.status[key]) {
                case 0:  tmp.setAttribute('class','rtn no_res');
                    break;
                case 1:  tmp.setAttribute('class','rtn success');
                    break;
                case 2:  tmp.setAttribute('class','rtn none');
                    break;
                case 3:  tmp.setAttribute('class','rtn fail');
                    break;
                default:  tmp.setAttribute('class','rtn no_res');
                    break;
            }
            me.output.appendChild(tmp);
            //console.log("KEY IS: "+key);
            //console.log(me.scanned.id[key]+' '+me.scanned.status[key]);
        }*/
        //NEW LOOP FOR PRINTING IN REVERSE... MAKES LIST LOOK PREPENDED
        for(var i = me.scanned.id.length - 1; i >= 0; i-- ) {
            var tmp = document.createElement('li');
            tmp.innerHTML = me.scanned.id[i];
            switch(me.scanned.status[i]) {
                case 0:  tmp.setAttribute('class','rtn no_res');
                    break;
                case 1:  tmp.setAttribute('class','rtn success');
                    break;
                case 2:  tmp.setAttribute('class','rtn none');
                    break;
                case 3:  tmp.setAttribute('class','rtn fail');
                    break;
                default:  tmp.setAttribute('class','rtn no_res');
                    break;
            }
            me.output.appendChild(tmp);
            //console.log("KEY IS: "+i);
            //console.log(me.scanned.id[i]+' '+me.scanned.status[i]);
        }
    }
    this.set_status = function(data) {
        //console.log('#---------- RETURN OBJECT');
        //console.log(data);
        var rtn_stat = 0;
        if(data.GOOD) {
            rtn_stat = 1;
        }
        else if(data.NONE) {
            rtn_stat = 2;
        }
        else if(data.ERR) {
            rtn_stat = 3;
        }
        
        if(data.tape_id) {
            var key = contains(me.scanned.id, data.tape_id, 1);
            //console.log("KEY is : "+key);
            //console.log("Return STAT:"+rtn_stat)
            if(key != -1) {
                me.scanned.status[key] = rtn_stat;
            }
        }
        me.display_list();
       //setTimeout( me.display_list, 2000);
    //console.log("RETURN ID = "+data.tape_id);
    }
    this.group_status = function() {
        return !(contains(me.scanned.status,3) && contains(me.scanned.status,0));
    }
    this.init();
}

/*
 * function tmp2(cont_div) {
    var me = this;
    this.root = cont_div;
    this.fields = {'tape_id':'','date':'','ven_id':'','loc_id':'','mtype':'','po_num':''};
    this.values =  {'tape_id':'','date':'','ven_id':'','loc_id':'','mtype':'','po_num':'', 'uname':'TEST'};
    this.form_div;
    this.results_div ;
    this.scanned = new Object();
    this.scanned.id = new Array();
    this.scanned.status = new Array();
    
    this.final_init = function(data) {
        me.fields.ven_id = selMenu(data.vendors);
        lf_pair(me.form_div, "Vendors", me.fields.ven_id);
        me.fields.loc_id = selMenu(data.loc);
        lf_pair(me.form_div, "Locations", me.fields.loc_id);
        me.fields.mtype = selMenu(data.mtype);
        lf_pair(me.form_div, "Media Type", me.fields.mtype);
       
        me.fields.date = tInput();
        lf_pair(me.form_div, "Date", me.fields.date);
 
        me.fields.po_num = tInput();
        me.fields.po_num.value = '11111';
        lf_pair(me.form_div, "PO#", me.fields.po_num);
        
         me.fields.tape_id = tInput();
         add_event(me.fields.tape_id, 1, me.input)
        lf_pair(me.form_div, "Tape ID", me.fields.tape_id);
        ui_date(me.fields.date);
       
    }
    this.init = function() {
        this.form_div = document.createElement('div');
        this.form_div.setAttribute('class','form1');
        this.results_div = document.createElement('div');
        this.results_div.setAttribute('class','results');
        this.root.appendChild(this.form_div);
        this.root.appendChild(this.results_div);
        getData(JSON.stringify({'args':{}, 'func':'getAll'}), this.final_init, true);
    }
   this.display_list = function() {
        me.results_div.innerHTML = '';
        for(var key in me.scanned.id) {
            if(key == 'contains')
                continue;
            var tmp = document.createElement('li');
            tmp.innerHTML = me.scanned.id[key];
            switch(me.scanned.status[key]) {
                case 0:  tmp.setAttribute('class','rtn no_res');
                    break;
                case 1:  tmp.setAttribute('class','rtn success');
                    break;
                case 2:  tmp.setAttribute('class','rtn none');
                    break;
                case 3:  tmp.setAttribute('class','rtn fail');
                    break;
                default:  tmp.setAttribute('class','rtn no_res');
                    break;
            }
            me.results_div.appendChild(tmp);
            //console.log("KEY IS: "+key);
            //console.log(me.scanned.id[key]+' '+me.scanned.status[key]);
        }
    }
    this.submit_callback = function(data) {
        var rtn_stat = 0;
        if(data.GOOD) {
            rtn_stat = 1;
        }
        else if(data.NONE) {
            rtn_stat = 2;
        }
        else if(data.ERR) {
            rtn_stat = 3;
        }   
        if(data.tape_id) {
            var key = me.scanned.id.contains(data.tape_id, 1);
            //console.log("KEY is : "+key);
            //console.log("Return STAT:"+rtn_stat)
            if(key != -1) {
                me.scanned.status[key] = rtn_stat;
            }
        }
        me.display_list();
       //setTimeout( me.display_list, 2000);
    //console.log("RETURN ID = "+data.tape_id);
    }
    this.updateValues = function() {
        for(var key in me.values) {
            if(me.fields[key]) {
                me.values[key] = me.fields[key].value;
                //console.log("val: "+me.values[key]);
            }
        }
        
    }
    this.submit = function () {
        getData(JSON.stringify({'args':me.values, 'func':'addNewTape'}), me.submit_callback, true);
    }
 
    this.input = function () {
        var str = this.value;
        var match = '';
        if((match = str.match(/([0-9]{5,6}L[0-9])/)) && !(me.scanned.id.contains(match[0]))) {
            this.style.backgroundColor ='green';
            this.style.color = 'white';
            var tmp = this;
           setTimeout(function() {tmp.style.backgroundColor ='white'; tmp.style.color ='black'}, 500);
            me.scanned.id.push(match[0]);
            me.scanned.status.push(0);
            me.updateValues();
            this.value = '';
            me.display_list();
            me.submit();
        }
        else if(str.length > 7){
            this.style.backgroundColor ='red';
            this.style.color ='white';
            var tmp = this;
           setTimeout(function() {tmp.style.backgroundColor ='white'; tmp.style.color="black"; tmp.value='';}, 2000);
        }
    }
}

 */
