   // Source: (http://www.codeproject.com/KB/scripting/jvk.aspx)
   // Parts of the following code are taken from the DocumentSelection
   // library (http://debugger.ru/projects/browserextensions/documentselection)
   // by Ilya Lebedev. DocumentSelection is distributed under LGPL license
   // (http://www.gnu.org/licenses/lgpl.html).

   var opened = false, vkb = null;

   function keyb_change()
   {
     //document.getElementById("switch").innerHTML = (opened ? "Show keyboard" : "Hide keyboard");
     opened = !opened;

     if(opened && !vkb)
       init();
     else
       vkb.Show(opened);

   }

   // 'source' is the field which is currently focused:
   var source = null, insertionS = 0, insertionE = 0;

   var userstr = navigator.userAgent.toLowerCase();
   var safari = (userstr.indexOf('applewebkit') != -1);
   var gecko  = (userstr.indexOf('gecko') != -1) && !safari;
   var standr = gecko || window.opera || safari;

   function search_for_text_field(num)
   {
      var tg = document.getElementsByTagName("INPUT");

      if(tg && tg[num])
        return tg[num];
      else
      {
        var tg2 = document.getElementsByTagName("TEXTAREA");

        if(tg2 && tg2[tg.length - num])
           return tg2[tg.length - num];
      }

      return null;
   }

   // This function retrieves the source element
   // for the given event object:
   function get_event_source(e)
   {
     var event = e || window.event;
     return event.srcElement || event.target;
   }

   // This function binds 'handler' function to the 
   // 'eventType' event of the 'elem' element:
   function setup_event(elem, eventType, handler)
   {
     return (elem.attachEvent) ? elem.attachEvent("on" + eventType, handler) : ((elem.addEventListener) ? elem.addEventListener(eventType, handler, false) : false);
   }

   // By focusing the INPUT field we set the 'source'
   // to the newly focused field:
   function focus_keyboard(e)
   {
     source = get_event_source(e);
   }

   // This function slightly differs from one with the same name
   // in '4-test-fly' sample. Now it accepts not the id, but the
   // number (index in the INPUT elements array) of the INPUT field.
   function register_input_field(num)
   {
     var tg = document.getElementsByTagName("INPUT");

     if(tg && tg[num])
       setup_event(tg[num], "focus", focus_keyboard);
   }

   // The same for TEXTAREA fields:
   function register_textarea_field(num)
   {
     var tg = document.getElementsByTagName("TEXTAREA");

     if(tg && tg[num])
       setup_event(tg[num], "focus", focus_keyboard);
   }

   // This function enumerates and "registers" all INPUT fields
   // on the page:
   function register_text_fields()
   {
     var tg = document.getElementsByTagName("INPUT");

     if(tg)
     {
       for(var i = 0; i < tg.length; i++)
         register_input_field(i);
     }

     tg = document.getElementsByTagName("TEXTAREA");

     if(tg)
     {
       for(var i = 0; i < tg.length; i++)
         register_textarea_field(i);
     }
   }

   function init()
   {
     // Note: all parameters, starting with 3rd, in the following
     // expression are equal to the default parameters for the
     // VKeyboard object. The only exception is 18th parameter
     // (flash switch), which is false by default.

     vkb = new VKeyboard("keyboard",    // container's id
                   keyb_callback, // reference to the callback function
                   false,          // create the arrow keys or not? (this and the following params are optional)
                   false,          // create up and down arrow keys? 
                   false,         // reserved
                   false,          // create the numpad or not?
                   "",            // font name ("" == system default)
                   "14px",        // font size in px
                   "#000",        // font color
                   "#F00",        // font color for the dead keys
                   "#FFF",        // keyboard base background color
                   "#FFF",        // keys' background color
                   "#DDD",        // background color of switched/selected item
                   "#777",        // border color
                   "#CCC",        // border/font color of "inactive" key (key with no value/disabled)
                   "#FFF",        // background color of "inactive" key (key with no value/disabled)
                   "#F77",        // border color of the language selector's cell
                   true,          // show key flash on click? (false by default)
                   "#CC3300",     // font color during flash
                   "#FF9966",     // key background color during flash
                   "#CC3300",     // key border color during flash
                   false,         // embed VKeyboard into the page?
                   true,          // use 1-pixel gap between the keys?
                   0);            // index(0-based) of the initial layout

     // The very 1st (index == 0) field is "focused" by default:
     source = search_for_text_field(0);

     // Any INPUTs? Register them all!
     if(source) register_text_fields();

     source.focus();
   }

   // Advanced callback function:
   //
   function keyb_callback(ch)
   {
     var val = source.value;

     switch(ch)
     {
       case "BackSpace":
         if(val.length)
         {
           var span = null;

           if(document.selection)
             span = document.selection.createRange().duplicate();

           if(span && span.text.length > 0)
           {
             span.text = "";
             getCaretPositions(source);
           }
           else
             deleteAtCaret(source);
         }

         break;

       case "<":
         if(insertionS > 0)
           setRange(source, insertionS - 1, insertionE - 1);

         break;

       case ">":
         if(insertionE < val.length)
           setRange(source, insertionS + 1, insertionE + 1);

         break;

       case "/\\":
         if(!standr) break;

         var prev  = val.lastIndexOf("\n", insertionS) + 1;
         var pprev = val.lastIndexOf("\n", prev - 2);
         var next  = val.indexOf("\n", insertionS);

         if(next == -1) next = val.length;
         var nnext = next - insertionS;

         if(prev > next)
         {
           prev  = val.lastIndexOf("\n", insertionS - 1) + 1;
           pprev = val.lastIndexOf("\n", prev - 2);
         }

         // number of chars in current line to the left of the caret:
         var left = insertionS - prev;

         // length of the prev. line:
         var plen = prev - pprev - 1;

         // number of chars in the prev. line to the right of the caret:
         var right = (plen <= left) ? 1 : (plen - left);

         var change = left + right;
         setRange(source, insertionS - change, insertionE - change);

         break;

       case "\\/":
         if(!standr) break;

         var prev  = val.lastIndexOf("\n", insertionS) + 1;
         var next  = val.indexOf("\n", insertionS);
         var pnext = val.indexOf("\n", next + 1);

         if( next == -1)  next = val.length;
         if(pnext == -1) pnext = val.length;

         if(pnext < next) pnext = next;

         if(prev > next)
            prev  = val.lastIndexOf("\n", insertionS - 1) + 1;

         // number of chars in current line to the left of the caret:
         var left = insertionS - prev;

         // length of the next line:
         var nlen = pnext - next;

         // number of chars in the next line to the left of the caret:
         var right = (nlen <= left) ? 0 : (nlen - left - 1);

         var change = (next - insertionS) + nlen - right;
         setRange(source, insertionS + change, insertionE + change);

         break;

       default:
         insertAtCaret(source, (ch == "Enter" ? (window.opera ? '\r\n' : '\n') : ch));
     }
   }

   // This function retrieves the position (in chars, relative to
   // the start of the text) of the edit cursor (caret), or, if
   // text is selected in the TEXTAREA, the start and end positions
   // of the selection.
   //
   function getCaretPositions(ctrl)
   {
     var CaretPosS = -1, CaretPosE = 0;

     // Mozilla way:
     if(ctrl.selectionStart || (ctrl.selectionStart == '0'))
     {
       CaretPosS = ctrl.selectionStart;
       CaretPosE = ctrl.selectionEnd;

       insertionS = CaretPosS == -1 ? CaretPosE : CaretPosS;
       insertionE = CaretPosE;
     }
     // IE way:
     else if(document.selection && ctrl.createTextRange)
     {
       var start = end = 0;
       try
       {
         start = Math.abs(document.selection.createRange().moveStart("character", -10000000)); // start

         if (start > 0)
         {
           try
           {
             var endReal = Math.abs(ctrl.createTextRange().moveEnd("character", -10000000));

             var r = document.body.createTextRange();
             r.moveToElementText(ctrl);
             var sTest = Math.abs(r.moveStart("character", -10000000));
             var eTest = Math.abs(r.moveEnd("character", -10000000));

             if ((ctrl.tagName.toLowerCase() != 'input') && (eTest - endReal == sTest))
               start -= sTest;
           }
           catch(err) {}
         }
       }
       catch (e) {}

       try
       {
         end = Math.abs(document.selection.createRange().moveEnd("character", -10000000)); // end
         if(end > 0)
         {
           try
           {
             var endReal = Math.abs(ctrl.createTextRange().moveEnd("character", -10000000));

             var r = document.body.createTextRange();
             r.moveToElementText(ctrl);
             var sTest = Math.abs(r.moveStart("character", -10000000));
             var eTest = Math.abs(r.moveEnd("character", -10000000));

             if ((ctrl.tagName.toLowerCase() != 'input') && (eTest - endReal == sTest))
              end -= sTest;
           }
           catch(err) {}
         }
       }
       catch (e) {}

       insertionS = start;
       insertionE = end
     }
   }

   function setRange(ctrl, start, end)
   {
     if(ctrl.setSelectionRange) // Standard way (Mozilla, Opera, Safari ...)
     {
       ctrl.setSelectionRange(start, end);
     }
     else // MS IE
     {
       var range;

       try
       {
         range = ctrl.createTextRange();
       }
       catch(e)
       {
         try
         {
           range = document.body.createTextRange();
           range.moveToElementText(ctrl);
         }
         catch(e)
         {
           range = null;
         }
       }

       if(!range) return;

       range.collapse(true);
       range.moveStart("character", start);
       range.moveEnd("character", end - start);
       range.select();
     }

     insertionS = start;
     insertionE = end;
   }

   function deleteSelection(ctrl)
   {
     if(insertionS == insertionE) return;

     var tmp = (document.selection && !window.opera) ? ctrl.value.replace(/\r/g,"") : ctrl.value;
     ctrl.value = tmp.substring(0, insertionS) + tmp.substring(insertionE, tmp.length);

     setRange(ctrl, insertionS, insertionS);
   }

   function deleteAtCaret(ctrl)
   {
     // if(insertionE < insertionS) insertionE = insertionS;
     if(insertionS != insertionE)
     {
       deleteSelection(ctrl);
       return;
     }

     if(insertionS == insertionE)
       insertionS = insertionS - 1;

     var tmp = (document.selection && !window.opera) ? ctrl.value.replace(/\r/g,"") : ctrl.value;
     ctrl.value = tmp.substring(0, insertionS) + tmp.substring(insertionE, tmp.length);

     setRange(ctrl, insertionS, insertionS);
   }

   // This function inserts text at the caret position:
   //
   function insertAtCaret(ctrl, val)
   {
     if(insertionS != insertionE) deleteSelection(ctrl);

     if(gecko && document.createEvent && !window.opera)
     {
       var e = document.createEvent("KeyboardEvent");

       if(e.initKeyEvent && ctrl.dispatchEvent)
       {
         e.initKeyEvent("keypress",        // in DOMString typeArg,
                        false,             // in boolean canBubbleArg,
                        true,              // in boolean cancelableArg,
                        null,              // in nsIDOMAbstractView viewArg, specifies UIEvent.view. This value may be null;
                        false,             // in boolean ctrlKeyArg,
                        false,             // in boolean altKeyArg,
                        false,             // in boolean shiftKeyArg,
                        false,             // in boolean metaKeyArg,
                        null,              // key code;
                        val.charCodeAt(0));// char code.

         ctrl.dispatchEvent(e);
       }
     }
     else
     {
       var tmp = (document.selection && !window.opera) ? ctrl.value.replace(/\r/g,"") : ctrl.value;
       ctrl.value = tmp.substring(0, insertionS) + val + tmp.substring(insertionS, tmp.length);
     }

     setRange(ctrl, insertionS + val.length, insertionS + val.length);
   }

