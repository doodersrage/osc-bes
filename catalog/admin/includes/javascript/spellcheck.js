
function getObject(obj) {
  var theObj;
  if(document.all) {
    if(typeof obj=="string") {
      return document.all(obj);
    } else {
      return obj.style;
    }
  }
  if(document.getElementById) {
    if(typeof obj=="string") {
      return document.getElementById(obj);
    } else {
      return obj.style;
    }
  }
  return null;
}

function CountInput(src) {
  var destin=getObject(src.name+'-count');
  var count=src.value.length;
  destin.innerHTML = count;
}

function win_pop(URL)
{	
  winname=window.open(URL,'WIN','width=600,height=380,left=210,top=210,resizable=yes,scrollbars=yes,status=yes'); 
  return winname;
}

function SpellCheck(form_name,field_name) 
{
  //read the form into the var: textform
  //if the form name is: proba 
  //and the field name is: description_short
  //we can read like this
  //var textform = self.document.proba.description_short.value;
  //var textform = self.document["proba"]["description_short"].value;
  
  var textform;
  var editor_obj  = getObject("_" +field_name+  "_editor");       // html editor object
  if (editor_obj) {
    textform = (editor_obj.tagName.toLowerCase() == 'textarea') ? editor_obj.value : editor_obj.contentWindow.document.body.innerHTML;
  } else { textform = self.document[form_name][field_name].value; }

  //example or writing to the form
  //self.document[form_name][field_name].value = "alabala i towa e";

  win_pop('');
  
  self.document.hidden_form.form_name.value = form_name; 	//w skritata forma se izpolzwa ime na pole form_name i field_name
  self.document.hidden_form.field_name.value = field_name;  	// koito nqmat nishto obshto s promrnliwite w tazi funkciq
  self.document.hidden_form.first_time_text.value = textform;
  self.document.hidden_form.submit();
}