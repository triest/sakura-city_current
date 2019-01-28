@extends('layouts.blog3', ['title' => 'Подтверждение SMS'])

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <label>Введите телефон. Имено этот номер будут видеть остальне пользователи. Менять его нельзя.
                Номер должен состоять из 11 цифр. Первая цифра код страны. Для России это 7. За тем идут 10 цифр номера телефона. Приме:79001234567</label>
                <input type="tel" class="form-control" id="phone" name="phone" onkeypress="return isNumber(event)"
                       required></input>
                <a class="button green" id="sendSMS">Отправить SMS</a><br>
                <label>Введите код</label>
                <input type="text" class="form-control" id="code" name="code" onkeypress="return isNumber(event)"
                       required></input>
                <a class="button green" id="inputCode2">Ввести код</a><br>

                <!--   <button id="button2" style="display:none;" hred="http://sakura-city.info/password/reset/" this.style.display = 'none';">Сбросить пароль</button>-->

                <button h role="link" id="createGirlPage" onclick=" relocate_home()" disabled>Создать анкету</button>
            </div>
        </div>
    </div>
    <a class="button blue" href="{{route('main')}}" role="link">К списку анкет</a>
    <!--   <button id="button2" style="display:none;" onclick="document.getElementById('button1').style.display = 'block'; this.style.display = 'none';">Сбросить пароль</button>
    -->



    <script src="https://unpkg.com/vue"></script>
    <script src="{{asset('js/vueScripts.js')}}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/vue"></script>
    <script>
        function relocate_home() {
            location.href = "http://sakura-city.info/createAnketPage";
        }

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
            if (phone.length != 11) {
                alert("Длина телефонного номера 11 цифр!")
                return;
            }
            axios.get('/sendSMS2?phone=' + phone)
                .then(
                    response => {
                        console.log(response.data);
                        if (response.data.result == "ok") {
                            alert("SMS отправленно")
                        }
                        if (response.data.result == "alredy") {
                            alert("Такой номер уже зарегистрирован!")
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
            $('#inputCode2').on('click', function (e) {
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
                axios.get('/sendCODE2?code=' + code + '&phone=' + phone)
                    .then(
                        response => {
                            console.log(response.data);
                            if (response.data.answer == "ok") {
                                var formID = document.getElementById("confurnButton")
                                console.log("ok");
                                document.getElementById('createGirlPage').disabled = false;
                            }
                            else if (response.data.result == "alredy") {
                                alert("Данный номер уже заригистрирован!")
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