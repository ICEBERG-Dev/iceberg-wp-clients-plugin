
function extractNumbers(phone) {
    return phone.replace(/\D/g, '');
  }
  
  
  function iceberg_crm_callbac_sendFormData() {
    // Get form values
    var name = document.getElementById("callbackNameClient").value;
    var phone = document.getElementById("callbackPhoneClient").value;
    var message = document.getElementById("callbackMessageClient").value;
  
    if (!(+extractNumbers(phone)>0)){
      document.getElementById("callbackPhoneClient").parentElement.parentElement.style = "border: solid 0.5px red !important"
      return
    }else{
      document.getElementById("callbackPhoneClient").parentElement.parentElement.style = "border: none !important"
    }
  
    if (message === ""){
      document.getElementById("callbackMessageClient").parentElement.parentElement.style = "border: solid 0.5px red !important"
      return
    }else{
      document.getElementById("callbackMessageClient").parentElement.parentElement.style = "border: none !important"
    }
  
    // Combine form values into a JSON object
    var formData = {
        "name": name,
        "phone": +extractNumbers(phone),
        "comment": message
    };
    document.querySelector('.callback-widget-btn').disabled = true
    // Send data to plugin's PHP file using AJAX
    jQuery.ajax({
        url: "/wp-content/plugins/iceberg-crm-callback/admin/iceberg_crm_callback_send_form.php",
        type: "post",
        data: formData,
        success: function(response) {
          document.querySelector('.callback-widget-btn').removeAttribute("disabled")
          document.querySelector('.callback-widget-btn').innerText = "Отправлено!"
          setTimeout(() => {
              document.getElementById("callbackNameClient").value = "";
              document.getElementById("callbackMailClient").value = "";
              document.getElementById("callbackPhoneClient").value = "";
              document.getElementById("callbackMessageClient").value = "";
              toggleModalCallbackWidget('callbackModal', 'callbackModalBG')
          }, 300)
          
            // Handle response from the server
            if (response === "OK") {
                console.log("Form data was sent successfully!");
            } else {
                console.log("Error: " + response);
            }
        }
    });
  }
  
  
  // -----------
  
  function toggleModalCallbackWidget(modal, bg) {
    document.querySelector(`#${modal}`).classList.toggle('active')
    document.querySelector(`#${bg}`).classList.toggle('active')
  
  }
  
  function nameManagerGenerate() {
    let min = 2
    let max = 10
    let random = Math.floor(min - 0.5 + Math.random() * (max - min + 1))
  
    let names = {
        "1": "Елена",
        "2": "Татьяна",
        "3": "Василиса",
        "4": "Алиса",
        "5": "Анастасия",
        "6": "Ольга",
        "7": "Альбина",
        "8": "Ксения",
        "9": "Виктория",
        "10": "София",
    }
  
    return names[random]
  }
  
  function phoneMaskOnLoad(){
      let input = document.querySelector('#callbackPhoneClient')
  
      const prefixNumber = (str) => {
          if (str === "7") {
              return "7 ("
          }
          if (str === "8") {
              return "8 ("
          }
          if (str === "9") {
              return "7 (9"
          }
          return "7 ("
      }
  
      input.addEventListener("input", (e) => {
          const value = input.value.replace(/\D+/g, "")
          const numberLength = 11
  
          let result
          if (input.value.includes("+8") || input.value[0] === "8") {
              result = ""
          } else {
              result = "+"
          }
  
          for (let i = 0; i < value.length && i < numberLength; i++) {
              switch (i) {
                  case 0:
                      result += prefixNumber(value[i])
                      continue;
                  case 4:
                      result += ") "
                      break;
                  case 7:
                      result += "-"
                      break;
                  case 9:
                      result += "-"
                      break;
                  default:
                      break;
              }
              result += value[i]
          }
          input.value = result
      })
  
      document.querySelector('#callbackManagerName').innerHTML = nameManagerGenerate()
  
  }
  
  function nameManOnLoad(){
    let input = document.querySelector('#callbackPhoneClient')
  
    const prefixNumber = (str) => {
        if (str === "7") {
            return "7 ("
        }
        if (str === "8") {
            return "8 ("
        }
        if (str === "9") {
            return "7 (9"
        }
        return "7 ("
    }
  
    input.addEventListener("input", (e) => {
        const value = input.value.replace(/\D+/g, "")
        const numberLength = 11
  
        let result
        if (input.value.includes("+8") || input.value[0] === "8") {
            result = ""
        } else {
            result = "+"
        }
  
        for (let i = 0; i < value.length && i < numberLength; i++) {
            switch (i) {
                case 0:
                    result += prefixNumber(value[i])
                    continue;
                case 4:
                    result += ") "
                    break;
                case 7:
                    result += "-"
                    break;
                case 9:
                    result += "-"
                    break;
                default:
                    break;
            }
            result += value[i]
        }
        input.value = result
    })
  
    document.querySelector('#callbackManagerName').innerHTML = nameManagerGenerate()
  }
  
  function iceberg_render_qr_for_link(){
  try {
      var qrcode = new QRCode("gen-qr-link-iceberg-crm-callback", {
          text: document.getElementById('gen-qr-link-iceberg-crm-callback-source').value,
          width: 256,
          height: 256,
          colorDark : "#535F65",
          colorLight : "#ffffff",
          correctLevel : QRCode.CorrectLevel.H
      });
  } catch (e){
  
  }
  }

  function setup_custom_btns(){
    document.querySelectorAll('#initIcebergCallBackWidget').forEach(element => {
        element.setAttribute('onclick', "toggleModalCallbackWidget('callbackModal', 'callbackModalBG')");
    })
  }
  
  
  jQuery(document).ready(function($){
      phoneMaskOnLoad()
      nameManOnLoad()
      iceberg_render_qr_for_link()
      setup_custom_btns()
  })