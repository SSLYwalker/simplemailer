<html>
    <head>
        <link rel="icon" href="images/sm_ico.png">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/humanity/jquery-ui.css">	
        <link rel="stylesheet" href="css/jquery.datetimepicker.css">
        <link rel="stylesheet" href="css/jquery-te-1.4.0.css">
	<link rel="stylesheet" href="css/base.css">
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script src="scripts/jquery.datetimepicker.js"></script>
        <script src="scripts/jquery-te-1.4.0.min.js"></script>
        <meta charset="UTF-8">
        <script type="text/javascript">
                /** Text resource-ok*/
		/**
                 * @brief Az oldal megjelnítési nyelvénk a kódja
                 * 
                 * @type String
                 */
                var gLang = 'hun';
                
                
        	var mui;
                /**
                 * @brief Ez a nyevi objektum tartalmazza a címkék nyelvi 
                 * megfelelőit
                 * 
                 * @type Object
                 */                
                var gSringRsourceObj={
					"hun" : {
                                                    "titleStr" : "Egyszerű levélküldő",
                                                    "submitCaption" : "Küldés",
                                                    "customerName" : "Ügyfél neve",
                                                    "toAddress" : "Címzett",
                                                    "carbonCopy" : "Másolat",
                                                    "blindCarbonCopy" : "Rejtett Másolat",
                                                    "newRecipient" : "Új címzett",
                                                    "delete" : "Törlés",
                                                    "product" : "Termék",
                                                    "msgLanguages": "Üzenet nyelve",
                                                    "hun": "Magyar",
                                                    "rom": "Román",
                                                    "cze": "Cseh",
                                                    "sky": "Szlovák",
                                                    "sz7" : "Szerviz7",
                                                    "mu" : "Modupro ULTIMATE",
                                                    "m" : "Modupro",
                                                    "qs" : "Qservice",
                                                    "start" : "Kezdés",
                                                    "stop" : "Befejezés",
                                                    "status": "Státusz",
                                                    "repair" : "Javítás",
                                                    "support" : "Segítségnyújtás",
                                                    "installation" : "Telepítés",
                                                    "activity" : "Tevékenység",
                                                    "recipientDeleteDialog" : "Valóban törölni szeretné a címzettet?",
                                                    "recipientDeleteDialogTitle" : "Címzett törlése",
                                                    "yes" : "Igen",
                                                    "no" : "Nem"
                                                },
					"eng" : {
                                                    "titleStr" : "Simple mailer",
                                                    "submitCaption" : "Send",
                                                    "customerName" : "Customer name",
                                                    "toAddress" : "To",
                                                    "carbonCopy" : "CC",
                                                    "blindCarbonCopy" : "BCC",
                                                    "newRecipient" : "New Recipient",
                                                    "delete" : "Delete",
                                                    "product" : "Product",
                                                    "msgLanguages": "Message Language",
                                                    "hun": "Hungarian",
                                                    "rom": "Romanian",
                                                    "cze": "Czech",
                                                    "sky": "Slovak",
                                                    "sz7" : "Szerviz7",
                                                    "mu" : "Modupro ULTIMATE",
                                                    "m" : "Modupro",
                                                    "qs" : "Qservice",
                                                    "start" : "Beginning",
                                                    "stop" : "Completion",
                                                    "status": "Status",
                                                    "repair" : "Repair",
                                                    "support" : "Support",
                                                    "installation" : "Installation",
                                                    "activity" : "Activity",
                                                    "recipientDeleteDialog" : "Do you want to delete the recipient?",
                                                    "recipientDeleteDialogTitle" : "Delete recipient",
                                                    "yes" : "Yes",
                                                    "no" : "No"
                                                }
                };
                
                
                
               
                /**
                 * @brief Az adatok ebben a tömmben kerülnek átadásra a feldolgozónak
                 * 
                 * @type Array
                 */
                var submitArray = [];
				
                /**
                 * @brief Egyedi azonosító generátor
                 * 
                 * @type Function
                 */
                var guid = (function() {
                        function s4() {
                                return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
                        }
                        return function() {
                                return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                                        s4() + '-' + s4() + s4() + s4();
                        };
                })();

                
               /**************************************************************
                * @brief Recipient holder osztály definició
                * 
                * @param {type} contentHtmlTag
                * @param {type} string_resource_obj
                * @param {type} parent
                * @returns {RecipientHolder}
                */
                function RecipientHolder(
                        contentHtmlTag, 
                        string_resource_obj, /**string tömb a megjelenítendő textekhez*/
                        parent        
                ) 
                {
                        this.contentHtmlTag = contentHtmlTag;
                        this.str_res_obj = string_resource_obj;
                        /**
                         * @brief A cymzetteket tároló tömb id, típus, email cím
                         */
                        this.recipientArr=new Array();
                        this.parent = parent;
                        this.lastRecipientType = 'to';
                        this.url;
                        this.ajaxData = {};
                }

                /**
                 * @brief Email input változáa esetén, az a fv ajaxos hívással 
                 * megjeleíti az az alternatívákat
                 * 
                 * @param {type} event
                 * 
                 * @returns Nothing
                 */
                RecipientHolder.prototype.liveSearch = function(event) {
                  var thisPoi = this;
                  var liveSearchDivParentId = $(event.target).parent().data('id');
                  var liveSearchDiv = $('#' + liveSearchDivParentId + '_emailinput_livesearch');
                  if (event.target.value.length===0) {
                        thisPoi.clearLiveSearchDiv(liveSearchDiv);
                        return;
                  }
                  
                  this.liveSearchAjaxSettings(liveSearchDivParentId);
             
                /*    
                  xmlhttp.onreadystatechange=function() {
                        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                          document.getElementById("livesearch").innerHTML=xmlhttp.responseText;
                          document.getElementById("livesearch").style.border="1px solid #A5ACB2";
                        }
                  }
                  xmlhttp.open("GET","livesearch.php?q="+str,true);
                  xmlhttp.send();*/
                };
                
                /*jquery elemként jön a paraméter*/
                RecipientHolder.prototype.clearLiveSearchDiv = function(liveSearchDiv){
                    liveSearchDiv.html('');
                    liveSearchDiv.css('border', '0px');
                }
                
                RecipientHolder.prototype.liveSearchResultItemOnClick = function(parentId, emailAddress){
                    var thisPoi = this;
                    alert('hopp');
                    var emailInput = $('#' + parentId +  '_emailinput');
                    console.log(emailInput);
                    emailInput.val('\'' + emailAddress + '\'').focus(); 
                    this.clearLiveSearchDiv();
                };
                
                RecipientHolder.prototype.liveSearchAjaxSettings = function(id){
                    var thisPoi = this;
                    thisPoi.ajaxData = {
                        "elementId" : id,
                        "action" : "liveSearch",
                        "pattern": $('#' + id + '_emailinput').val()
                    };
                    thisPoi.url = '/simplemailer/livesearch.php';
                    
                    thisPoi.callAjax();
                };
                
                /**
                 * A liveSearch eredményét megjelenítő fv
                 * 
                 * @see RecipientHolder.prototype.liveSearch
                 * 
                 * @param [in] response Az ajax hívás response-ja JSON
                 * 
                 * @returns {undefined}
                 */
                RecipientHolder.prototype.showLiveSearchResult = function(response, containerParentId){
                    var container = $('#' + containerParentId + '_emailinput_livesearch');
                    container.html(response);
                    
                   
                };
                
                /**
                 * @brief Általános ajax hívást megvalósító fv.
                 * 
                 */
                RecipientHolder.prototype.callAjax = function(){
                    var thisPoi = this;
                    $.ajax({
                     url: thisPoi.url,
                     method: 'post',
                     async: false,
                     data: thisPoi.ajaxData  
                    }).done(function(response){
                     //var responseJSON = $.parseJSON(response);
                     var responseJSON = response;
                     switch(thisPoi.ajaxData.action){
                      case 'liveSearch' : {
                       thisPoi.showLiveSearchResult(responseJSON, thisPoi.ajaxData.elementId);
                       $('.live-search-item').on('click', function(event){
                       var recipientHolderDiv = $('#' + $(event.target).data('containerid'));
                       console.log(recipientHolderDiv);
                    });
                       break;
                      }
                      case 'getNews' : {
                       thisPoi.doNews(responseJSON);
                       break;
                      }   
                      case 'login' : {
                       thisPoi.doLogin(responseJSON);
                       break;
                      }   
                     }
                    }); 
                };
                
               /**
                * Címett adati tárolja le egy tömbben
                * 
                * @param {type} typep  Címzett típusa
                * @param {type} addressp [in] Címzett email címe
                * 
                * @returns {String} A címzett egyedi azonosítója a tömbben
                */
                RecipientHolder.prototype.AddItem=function(typep, addressp){
                        var id=guid();

                        this.recipientArr.push({
                                "id" : id,
                                "type" : typep,
                                "address" : addressp
                        });
                        return id;
                };
                
                /**
                 * uuid alapján visszaadja az elem tárolás pozívióját
                 * 
                 * @param array [in] Tároló tömb
                 * @param uuid [in] Egyedi azonosító
                 * 
                 * @returns A keresett elem indexével.
                 */
                RecipientHolder.prototype.getIndex=function(array, uuid){
                        for(var i=0; i<array.length; i++){
                            var index = array[i]['id'].indexOf(uuid);
                            if (index > -1){
                                return i;
                            };
                        };
                };
                
                /**
                *  @brief uuid alapján egy címzett elemet eltávolít a tárolótömbből
                *  
                *  @see AddItem();
                *  
                *  @param array [in] A tárolótömb.
                *  @param uuid [in] Az elem egyedi azonosítója.
                */
                RecipientHolder.prototype.removeItem=function(array, uuid){
                    var index = this.getIndex(array, uuid);
                    array.splice(index,1);
                };

				/**
                *  @brief A címzett elem tualjdonságait állatja be a tárolótömbben
                *  
                *  @param array [in] A tárolótömb.
				*  @param type [in] Címzett típusa.
                *  @param uuid [in] Az elem egyedi azonosítója.
				*  @param value [in] Az email cím.
                */
                RecipientHolder.prototype.setProperty=function(array, type, uuid, value){
                    var index = this.getIndex(array, uuid);
                    array[index][type] = value;
                };
                
		/**
                *  @brief Validáló fv.
                */
                RecipientHolder.prototype.validate = function() {
                    //alert('validálás');
                    
                    /*dtpicker validate*/
                    var dtpicker = $('#'+this.parent.contentHtmlTag+'_status_dtpicker').val();
                    if(dtpicker !== ''){
                        //alert('dtpicker validate' + this.parent.currentdate+' '+this.parent.currenttime);
                    }
                };
                
				/**
                *  @brief Beállítja mi volt az utolsó címzettípus
                */
                RecipientHolder.prototype.setLastRecipientType = function() {
                    for(var i=0; i<this.recipientArr.length; i++)
                    {
                        if(i === this.recipientArr.length-1){
                            this.lastRecipientType = this.recipientArr[i]['type'];
                        }
                    }
                };
                
				/**
                *  @brief uuid alapján visszadja, hogy az aktuális elem az utolsó-e
                *  
                *  @param uuid [in] Az elem egyedi azonosítója.
                */
                RecipientHolder.prototype.isLastItem = function(uuid) {
                    var retval = false;
                    
                    var array = this.recipientArr;
                    var index = this.getIndex(array, uuid);
                    if(index === this.recipientArr.length-1){
                        retval = true;
                    }
                    return retval;
                };
                
				/**
                *  @brief uj cimzett hozzáadása
                */
                RecipientHolder.prototype.newRecipient = function(){
                    var id = this.AddItem(this.lastRecipientType,"");
                    this.show();
                    //$("div").find("[data-id='" + id + "'] input").focus();
					$('div[data-id = ' + id + '] input').focus();
                };
                
				/**
                *  @brief Törlési párbeszédablak
                */
                RecipientHolder.prototype.getDialogsStr = function(){
                    var cont = '';
                    
                    cont+='<div id="'+this.contentHtmlTag+'_recipient_delete_dialog" title="'+this.str_res_obj[gLang]['recipientDeleteDialogTitle']+'">';
                    cont+='<p>' + this.str_res_obj[gLang]['recipientDeleteDialog'] + '</p>';
                    cont+='</div>';
                    
                    return cont;
                };

                RecipientHolder.prototype.show=function(){
                        var cont='';
                        cont+='<div id="'+this.contentHtmlTag+'_recipientholderdiv" class="recipientholderdiv">';
                        cont+='<button type="button" id="new_recipient" href="#">'+this.str_res_obj[gLang]['newRecipient']+'</button>';
                        cont+=this.getDialogsStr();
                        cont+='</div>';
                        
                         $('#'+this.contentHtmlTag).html(cont);
                        // jquery kontrolok inicializálása
                        for(var i=0; i<this.recipientArr.length; i++)
                        {
                                if(i===0){
                                    editable = true;
                                    deletable = false;
                                } else {
                                    editable = true;
                                    deletable = true;
                                }
                                var item=this.recipientArr[i];
                                $('#'+this.contentHtmlTag+'_recipientholderdiv').append(this.getRecipientDivStr(item['id'],editable, deletable));
                                $('div').find('[data-id="' + item['id'] + '"] select').val(item['type']);
                                $('div').find('[data-id="' + item['id'] + '"] input').val(item['address']);
                                this.setLastRecipientType();
                        }
                        
                        /*ESEMÉNYKEZELÉS*/
                        /*jQuery doesn't redefine the this pointer, but that's how JavaScript functions work in general. Store a reference to the this pointer under a different name, and use that.*/
                        var thisPoi = this;
                        /*Új címzett hozzáadása*/
                        $('#new_recipient').click(function(){
                            thisPoi.newRecipient();
                        });
                        
                        
                        
                        /*Ha enter-t nyomtunk, nem üres az inputfield és az utolsó email-inputon állunk - új címzettet hozlétre
						* Ha BACKSPACE-t nyomtunk és üres az input - prevent default máskülönben és delete dialog vagy livesearch
						*/
                        $(".email-input").on('keyup', function(event){
                            /*email input ertekkiaras az recipientArr tombbe*/
                            var type = 'address';
                            var uuid = $(this).parent().data("id");
                            var value = $(this).val();
                            thisPoi.setProperty(thisPoi.recipientArr, type, uuid, value);
                            /*ENTER nyomása és nem üres input esetén új címzett*/
                            var inputvalue = $(this).val();
                            var uuid = $(this).parent().data("id");
                            var lastitem = thisPoi.isLastItem(uuid);							
							
							if(event.which === 8){
								if(inputvalue === '' && $(this).parent().data('deletable') === true){
									
									$(this).parent().addClass('marked-for-delete');
									alert('#'+thisPoi.contentHtmlTag+'_recipient_delete_dialog');
									$('#'+thisPoi.contentHtmlTag+'_recipient_delete_dialog').dialog('open');
									/*kivédi, hogy a BACKSPACE hatására visszanavigáljon az előző oldalra*/
									event.defaultPrevented;
								}
							}
							
                            if(event.which === 13 && inputvalue !== '' && lastitem === true){
                                thisPoi.newRecipient();
                            } else {
				thisPoi.liveSearch(event);
                            }
                        });
						
						
                        
                        $(".noneditable").attr("disabled","disabled");
                        
                        $(".dovalidate").keyup(function(){
                            thisPoi.validate();
                        });
                        $(".dovalidate").click(function(){
                            thisPoi.validate();
                        });
                        $(".dovalidate").change(function(){
                            thisPoi.validate();
                        });
                        
                        /*select onchange ertekkiaras az recipientArr tombbe*/
                        $(".recipient-type").change(function(){
                            var type = 'type';
                            var uuid = $(this).parent().data("id");
                            var value = $(this).val();
                            thisPoi.setProperty(thisPoi.recipientArr, type, uuid, value);
                            thisPoi.setLastRecipientType();
                        });

                        /*fv*/
                        /*jQuery.fn.myvalidate = function() {
                            var uid = $(this).parent().data("id");
                            alert(uid);
                            if($(this).val() !== '')
                            {
                                $('#' + uid + 'emailinput_info').css("color","red");
                                $('#'+thisPoi.parent.contentHtmlTag+'_submitbutton').removeAttr("disabled");
                            } 
                            else 
                            {   
                                alert('#'+thisPoi.parent.contentHtmlTag+'_submitbutton');
                                $('#'+thisPoi.parent.contentHtmlTag+'_submitbutton').attr("disabled","disabled");
                            }
                        };*/

                        
                        $('.email-input').on('change', function(){
                            thisPoi.validate();
                        });
                        

                        /**Címzett eltávolítása dialógus*/
                        var delDialog = $( '#'+thisPoi.contentHtmlTag+'_recipient_delete_dialog' ).dialog({
                                            autoOpen: false,
                                            height: 400,
                                            width: 350,
                                            modal: true,
                                            buttons: {
                                              "Create an account": function(){},
                                              Cancel: function() {
                                                delDialog.dialog( "close" );
                                              }
                                            },
                                            close: function() {

                                            }
                                          });
                        $(function() {
                                 /*   $('#'+thisPoi.contentHtmlTag+'_recipient_delete_dialog').dialog({
                                    autoOpen: false,
                                    modal: true,
                                    resizable: false,
                                    dialogClass: "recipient-delete-dialog",
                                    /*megnyitáskor az igen gombra kerül a fókusz és ebben az esetben
                                       a BACKSPACE megnyomását védeni kell mert másképp a böngésző betölti az előző oldalt*/
                                    /*open: function(){ 
                                            $(".recipient-delete-dialog").find("button").on("keydown", function(){
                                                if(event.which === 8){
                                                    event.defaultPrevented;
                                                }
                                            });
                                        },
                                    buttons: [
                                        {
                                        text: thisPoi.str_res_obj[gLang]['yes'],
                                        click: function() {
                                                    var target = $(".marked-for-delete");
                                                    var uuid = target.data("id");
                                                    thisPoi.removeItem(thisPoi.recipientArr, uuid);
                                                    thisPoi.setLastRecipientType();
                                                    target.remove();
                                                    $(this).dialog("close");
                                                    $(".email-input:last").focus();
                                                }
                                        },
                                        {
                                        text: thisPoi.str_res_obj[gLang]['no'],
                                        click: function() {
                                                    $(".marked-for-delete").removeClass("marked-for-delete");
                                                    $(this).dialog("close");
                                                }
                                            }
                                    ]
						});*/
                        
                                    $('button.delete-recipient').on('click', function() {
                                                    $(this).parent().addClass("marked-for-delete");
                                                    $(".email-input").focus();
                                                    //$('#'+thisPoi.contentHtmlTag+'_recipient_delete_dialog').dialog("open");
                                                    delDialog.dialog("open");

                                    });
                        
						});

                        /*ESEMÉNYKEZELÉSVÉGE*/
                        
                        $('.email-input:last').focus();
                        $('#new_recipient').button();
                        $('.delete-recipient').button({
                            icons: {primary: 'ui-icon-trash'}
                        });
                };

                RecipientHolder.prototype.getitembyid=function(id){
                        var retitem=null;
                        for(var i=0; i<this.recipientArr.length; i++)
                        {
                                var item=this.recipientArr[i];
                                if(item['id'] === id)
                                {
                                        retitem=item;
                                        break;
                                }
                        }	
                        return retitem;
                };

                RecipientHolder.prototype.getRecipientDivStr = function(id,editablep,deletablep)
                {
                        var cont='';
                        var editable='';
                        if(editablep !== true ){
                            editable = 'noneditable';
                        };
                        
                        var item=this.getitembyid(id);
                        if(item !== null)
                        {
                                cont+='<div class="recipient-item" data-id="'+id+'" data-deletable="'+deletable+'">';
                                cont+='<select class="recipient-type dovalidate '+editable+' ui-widget ui-widget-input ui-corner-all">';
                                cont+='<option value="to">'+this.str_res_obj[gLang]['toAddress']+'</option>';
                                cont+='<option value="cc">'+this.str_res_obj[gLang]['carbonCopy']+'</option>';
                                cont+='<option value="bcc">'+this.str_res_obj[gLang]['blindCarbonCopy']+'</option>';
                                cont+='</select>';
                                cont+='<input type="email" id="'+id+'_emailinput" class="email-input dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                                if(deletablep === true){
                                    cont+='<button class="delete-recipient" type="button" href="#">'+this.str_res_obj[gLang]['delete']+'</button>';
                                }
                                cont+='<div id="'+id+'_emailinput_info">';
                                cont+='teszt';
                                cont+='</div>';
				cont+='<div id="'+id+'_emailinput_livesearch" class="live-search-div">';
                                cont+='';
                                cont+='</div>';
                                //cont+=item['id']+','+item['type']+','+item['address']; 
                                cont+='</div>';
                        }
                        return cont;
                };

                ///////////////////////////////////////////////////////////////////////////////////////////////
                // Mailer user interface osztály definició
                function MailerUserInterface(
                        contentHtmlTag, 
                        string_resource_obj /*string tömb a megjelenítendő textekhez*/
                ) 
                {
                        this.contentHtmlTag = contentHtmlTag;
                        this.str_res_obj = string_resource_obj;
                        this.recipientArr = new Array({
                            "customerName" : 'üres',
                            "lang" : 'hun',
                            "products" : 'sz7',
                            "activity" : 'repair',
                            "status" : 'start',
                            "message": ''
                        });
                        this.recipholder = new RecipientHolder(this.contentHtmlTag+'_recipientholder', string_resource_obj, this);
                        this.recipholder.AddItem("to", ""); 
                        //this.recipholder.AddItem("cc", "csernyey.krisztian@3szs.hu"); 
                        this.recipholder.AddItem("bcc", "zk@3szs.hu"); 				
                        this.dateformat='Y.m.d.';
                        this.timeformat='H:i';
                        this.currentdate='';
                        this.currenttime='';
                }
				
		/**
                *  @brief Cation szövegek kinyerése a string resource objektumból
                *  
                *  @param lang [in] Nyelv.
                *  @param mit [in] Caption.
		*
		*  @return string
                */
		MailerUserInterface.prototype.getCaption = function(lang, mit){
			var retval = 'Caption Str';
			retval = this.str_res_obj[lang][mit];
			return retval;
		};
                
                MailerUserInterface.prototype.validate = function() {
                    alert('MailerUserInterface validálás');
                    
                    /*Name validate*/
                    var customerName = $('#'+this.parent.contentHtmlTag+'_customerName').val();
                    if(customerName === ''){
                        alert('üres ügyfélnév');
                    }
                };

                MailerUserInterface.prototype.show = function() {
                        var cont='';
                        /*osztály pointer deklarálása*/
                        var thisPoi = this;
                        /*recipholder pointer létrehozása*/
                        var rhpoi = thisPoi.recipholder;
                        
                        
                        cont=thisPoi.getFormStr();
                         $('#'+thisPoi.contentHtmlTag).html(cont);
                        thisPoi.setTitle(thisPoi.getCaption(gLang, 'titleStr'));
                        // jquery ui-s kontrolok inicializálása
                        thisPoi.initJqueryControls();
                        thisPoi.recipholder.show();
                        
                        $('#'+thisPoi.contentHtmlTag+'_customerName').on("input", function(){
                            thisPoi.recipientArr[0]["customerName"] = $(this).val();
                        });
                        
                        $('#'+thisPoi.contentHtmlTag+'_lang').on("input", function(){
                            thisPoi.recipientArr[0]["lang"] = $(this).val();
                            $(".jqte_editor").load('./templates/'+$(this).val()+'_template');
                            thisPoi.recipientArr[0]["message"] = $(".jqte_editor").html();
                        });
                        
                        $('#'+thisPoi.contentHtmlTag+'_products').on("input", function(){
                            thisPoi.recipientArr[0]["products"] = $(this).val();
                        });
                        
                        $('#'+thisPoi.contentHtmlTag+'_activity').on("input", function(){
                            thisPoi.recipientArr[0]["activity"] = $(this).val();
                        });
                        
                        $('#'+thisPoi.contentHtmlTag+'_status').on("input", function(){
                            thisPoi.recipientArr[0]["status"] = $(this).val();
                        });
                        
                        $('#'+thisPoi.contentHtmlTag+'_status_dtpicker').on("change", function(){
                            thisPoi.recipientArr[0]["datetime"] = $(this).val();
                        });
                        
                        $(".jqte_editor").on("input", function(){
                            thisPoi.recipientArr[0]["message"] = $(this).html();
                        });
                        
                         $('#'+thisPoi.contentHtmlTag+'_submit_button').on("click", function() {
                                submitArray.push(rhpoi["recipientArr"]);
                                submitArray.push(thisPoi["recipientArr"]);
                                var myJson = JSON.stringify(submitArray);
                                $("#"+thisPoi.contentHtmlTag+"_hidden").val(myJson);
                                $("#"+thisPoi.contentHtmlTag+'_form').submit();
                                /*$.ajax({
                                    "async" : false
                                });
                                $.post('/mailkuldo/mailsender.php', {"tomb" : submitArray});*/
                                /*var s = '';                                
                                for (row in submitArray[1]){
                                    s+= row + "\n";
                                    for (v in submitArray[1][row]){
                                        s+= v + " - " + submitArray[1][row][v] + "\n";
                                    }
                                    
                                }
                                alert(s);
                                alert(submitArray);*/
                            });
                        
                };

                MailerUserInterface.prototype.initJqueryControls = function(){
                        var thisPoi=this;
                        $('#'+thisPoi.contentHtmlTag+'_submit_button').button({
                                icons : {primary : 'ui-icon-check'}
                        });
                        
                        var format=thisPoi.dateformat+' '+thisPoi.timeformat;

                        $('#'+thisPoi.contentHtmlTag+'_status_dtpicker').datetimepicker({
                            format:format,
                            lang:'hu',
                            startDate: '2014.01.01',
                            mask:true, // '9999/19/39 29:59' - digit is the maximum possible for a cell
                            step: 5,
                            dayOfWeekStart: 1,
                            onSelectDate:function(current_time,$input){
                                thisPoi.currentdate=current_time.dateFormat(thisPoi.dateformat);
                            },
                            onSelectTime:function(current_time,$input){
                                //thisPoi.currentdate=current_time.dateFormat(thisPoi.dateformat);
                                thisPoi.currenttime=current_time.dateFormat(thisPoi.timeformat);
                            }
                            
                        });
                        $(".editor").jqte();
                        $(".jqte_editor").load('./templates/hun_template', function( response, status, xhr ) {
                            if ( status !== "error" ) {
                             thisPoi.recipientArr[0]["message"] = $(".jqte_editor").html();
                            }
                        });
                        //$('#'+recipientholder.contentHtmlTag+'_recipientholderdiv div:first button:first').focus();
                        
                        //$("#"+this.contentHtmlTag+"_langdiv").on("change", function(){$(".editor").jqteVal("New article!");});

                };

                MailerUserInterface.prototype.setTitle = function(title) {
                        var cont='';

                         $('#htmltitle').remove();
                        cont+='<title id="htmltitle">';
                        cont+=title;
                        cont+='</title>';
                         $('html head').append(cont);
                };

                MailerUserInterface.prototype.getFormStr = function() {
                        var cont='';

                        cont+='<form id="'+this.contentHtmlTag+'_form" method="POST" action="./mailsender.php" class="ui-widget-content ui-corner-all">';
                        cont+=this.getCustomerNameDivStr();
                        cont+=this.getLangDivStr();
                        cont+=this.getProductsDivStr();
                        cont+=this.getactivitydivstr();
                        cont+=this.getStatusDivStr();
                        cont+='<div id="'+this.contentHtmlTag+'_recipientholder">';
                        cont+='</div>';
                        cont+=this.geteditordivStr();
                        cont+=this.getButtonStr('button', this.str_res_obj[gLang]['submitCaption']);		
                        cont+='</form>';				

                        return cont;
                };

                MailerUserInterface.prototype.getButtonStr = function(type, caption){
                        var cont='';

                        cont+='<div id="'+this.contentHtmlTag+'_buttondiv" >';
                        cont+='<input id="'+this.contentHtmlTag+'_submit_button" type="'+type+'" value="'+caption+'">';
                        cont+='<input type="hidden" name="jsonize" id="'+this.contentHtmlTag+'_hidden" value="">';
                        cont+='</div>';

                        return cont;
                };

                MailerUserInterface.prototype.getCustomerNameDivStr = function (){
                        var cont='';

                        cont+='<div id="'+this.contentHtmlTag+'_customer_name_div">';
                        cont+='<label class="ui-widget" for="'+this.contentHtmlTag+'_customer_name">'+this.getCaption(gLang, 'customerName')+'</label>';
                        cont+='<input id="'+this.contentHtmlTag+'_customer_name" name="'+this.contentHtmlTag+'_customer_name" class="customer-name dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                        cont+='</div>';
                        return cont;
                };

                MailerUserInterface.prototype.getProductsDivStr = function (){
                        var cont='';

                        cont+='<div id="'+this.contentHtmlTag+'_productdiv">';
                        cont+='<label class="ui-widget" for="'+this.contentHtmlTag+'_products">'+this.str_res_obj[gLang]['product']+'</label>';
                        cont+='<select id="'+this.contentHtmlTag+'_products" class="product ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="sz7">'+this.str_res_obj[gLang]['sz7']+'</option>';
                        cont+='<option value="mu">'+this.str_res_obj[gLang]['mu']+'</option>';
                        cont+='<option value="m">'+this.str_res_obj[gLang]['m']+'</option>';
                        cont+='<option value="qs">'+this.str_res_obj[gLang]['qs']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };

                MailerUserInterface.prototype.getLangDivStr = function (){
                        var cont='';

                        cont+='<div id="'+this.contentHtmlTag+'_langdiv">';
                        cont+='<label class="ui-widget" for="'+this.contentHtmlTag+'_msgLanguages">'+this.str_res_obj[gLang]['msgLanguages']+'</label>';
                        cont+='<select id="'+this.contentHtmlTag+'_lang" class="product ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="hun">'+this.str_res_obj[gLang]['hun']+'</option>';
                        cont+='<option value="rom">'+this.str_res_obj[gLang]['rom']+'</option>';
                        cont+='<option value="cze">'+this.str_res_obj[gLang]['cze']+'</option>';
                        cont+='<option value="sky">'+this.str_res_obj[gLang]['sky']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };
                /**
                 * @bref info a fvrol
                 * 
                 * @returns {String}
                 */
                MailerUserInterface.prototype.getactivitydivstr = function (){
                        var cont='';

                        cont+='<div id="'+this.contentHtmlTag+'_activitydiv">';
                        cont+='<label class="ui-widget" for="'+this.contentHtmlTag+'_activity">'+this.str_res_obj[gLang]['activity']+'</label>';
                        cont+='<select id="'+this.contentHtmlTag+'_activity" class="activity-type ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="repair">'+this.str_res_obj[gLang]['repair']+'</option>';
                        cont+='<option value="support">'+this.str_res_obj[gLang]['support']+'</option>';
                        cont+='<option value="installation">'+this.str_res_obj[gLang]['installation']+'</option>';
                        cont+='</select>';
                        cont+='</div>';
                        return cont;
                };

                MailerUserInterface.prototype.getStatusDivStr = function (){
                        var cont='';

                        cont+='<div id="'+this.contentHtmlTag+'_statusdiv">';
                        cont+='<label class="ui-widget" for="'+this.contentHtmlTag+'_status">'+this.str_res_obj[gLang]['status']+'</label>';
                        cont+='<select id="'+this.contentHtmlTag+'_status" class="status-type ui-widget ui-widget-input ui-corner-all">';
                        cont+='<option value="start">'+this.str_res_obj[gLang]['start']+'</option>';
                        cont+='<option value="stop">'+this.str_res_obj[gLang]['stop']+'</option>';
                        cont+='</select>';
                        cont+='<input id="'+this.contentHtmlTag+'_status_dtpicker" class="dtpicker dovalidate ui-widget ui-widget-input ui-corner-all"/>';
                        cont+='</div>';
                        return cont;
                };
                
                MailerUserInterface.prototype.geteditordivStr = function (){
                        var cont='';

                        cont+='<div id="'+this.contentHtmlTag+'_editordiv">';
                        cont+='<textarea class="editor" id="'+this.contentHtmlTag+'_editor">szerkesztő</textarea>';
                        cont+='</div>';
                        return cont;
                };
                
                        $(document).ready(function(){
                        mui=new MailerUserInterface('content', gSringRsourceObj);
                        //var mui1=new MailerUserInterface('content1', gSringRsourceObj);				
                        mui.show();
                        mui.setTitle("Egyszerű levélküldő");
                        //mui1.show();		
                });
        </script>
			
    </head>
    <body>
        <div id="content">
        </div>
    </body>
	
</html>