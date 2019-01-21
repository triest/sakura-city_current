@extends('layouts.blog3', ['title' => 'Сброс пароля по SMS'])

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <label>Введите телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" onkeypress="return isNumber(event)"
                       required></input>
                <a class="button green" id="sendSMS">Отправить SMS</a><br>
                <label>Введите код</label>
                <input type="text" class="form-control" id="code" name="code" onkeypress="return isNumber(event)"
                       required></input>
                <a class="button green" id="inputCode">Ввести код</a><br>





                <!--   <button id="button2" style="display:none;" hred="http://sakura-city.info/password/reset/" this.style.display = 'none';">Сбросить пароль</button>-->


            </div>
        </div>
    </div>

    <!--   <button id="button2" style="display:none;" onclick="document.getElementById('button1').style.display = 'block'; this.style.display = 'none';">Сбросить пароль</button>
    -->



    <script src="https://unpkg.com/vue"></script>
    <script src="{{asset('js/vueScripts.js')}}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/vue"></script>
    <script>
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }


        $('#sendSMS').on('click', function (e) {
            console.log("click")
            var phone = document.getElementById('phone').value;
            console.log(phone)
            //ajax
            axios.get('/sendSMS2?phone=' + phone)
                .then(
                    response => {
                        console.log(response.data);
                        if (response.data.result == "ok") {
                            alert("SMS отправленно")
                        }
                        else {
                            alert("Ошибка. Проверьте номер!")
                        }
                    }
                )
                .catch(
                    // error=>console.log(error)
                )
        }),
            $('#inputCode').on('click', function (e) {
                console.log("input code")
                var code = document.getElementById('code').value;
                var phone = document.getElementById('phone').value;
                console.log(code)
                console.log(phone)
                //ajax
                /*  $.get('/sendSMS?phone='+phone,function (data) {
                      var obj = jQuery.parseJSON(data.body);
                      alert(obj.rezult);
                  })*/
                axios.get('/sendCODE?code=' + code + '&phone=' + phone)
                    .then(
                        response => {
                            console.log(response.data);
                            if (response.data.andwer == "ok") {
                                var formID = document.getElementById("passform")
                                formID.style.display = "block"
                                document.getElementById("user").value = response.data.token;
                            }
                            else {
                                alert("Ошибка. Неверный код!")
                            }

                            //    document.getElementById(button2.style.display = 'block')

                        }
                    )
                    .catch(
                        // error=>console.log(error)
                    )
            })


    </script>

@endsection
