@extends('layout')
@section('item', 'link_3')
@section('content')
    @include('setting.script_setting_app')
    <div class="p-4 mx-1 mt-1 bg-white rounded py-3">
        @include('div.TopServicePartner') <script>NAME_HEADER_TOP_SERVICE("Настройки → настройки интеграции")</script>
        @include('div.alert')
        @isset($message)
            <script>
                alertViewByColorName("danger", "{{ $message }}")
            </script>
        @endisset

        <form class="mt-3" action="/Setting/createAuthToken/{{ $accountId }}?isAdmin={{ $isAdmin }}" method="post" enctype="multipart/form-data">
        @csrf <!-- {{ csrf_field() }} -->

            <div class="row mt-3">
                <div class="col-3 mt-2"><label> <i class="fa-solid fa-handshake"></i> Выберите организацию </label></div>
                <div class="col-9">
                    <select id="organizationID" name="organizationID" class="form-select text-black" onchange="ID_BIN(this.value)">
                        @foreach($organization as $item)
                            <option value="{{$item->id}}"> {{$item->name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3 mt-2">
                   <i id="fa_yes_ID_BIN" style="display: none" class="fa-solid fa-circle-xmark text-danger"></i>
                    <i id="fa_no_ID_BIN" style="display: none" class="fa-solid fa-circle-check text-success"></i>
                    БИН компании: <span id="ID_BIN"></span></div>
                <div class="col-5">
                    <input onchange="View('PASS_ESF', 'fa_yes_ID_BIN', 'fa_no_ID_BIN')" id="PASS_ESF" type="text" name="PASS_ESF" placeholder="Пароль от ESF" class="form-control form-control-orange" required="" maxlength="255" value="">
                </div>
                <div class="col-3 mt-1 text-right">
                    <div>Пароль от ЭЦП идентичный ?</div>
                </div>
                <div class="col-1 mt-1 text-center">
                    <input class="form-check-input" type="checkbox" onchange="PASS_OR_MATCH(this.checked)">
                </div>
            </div>

            <div id="OBJECT_AUTH_RSA256">
                <div class="row mt-3 ml-2">
                    <div class="col-4 mt-1"> Выберите ЭЦП ключ AUTH_RSA256... </div>
                    <div class="col-8"> <input class="form-control" type="file" id="AUTH_RSA256" name="AUTH_RSA256"> </div>
                </div>
                <div class="row ml-2"> <div class="col-4"></div>
                    <div id="DIV_AUTH_RSA256" class="col-8">
                        <input id="PASS_AUTH_RSA256" type="text" name="PASS_AUTH_RSA256" placeholder="Пароль от ключа AUTH_RSA256" class="form-control form-control-orange">
                    </div>
                </div>
            </div>
            <div id="OBJECT_RSA256">
                <div class="row mt-3 ml-2">
                    <div class="col-4 mt-1"> Выберите ЭЦП ключ RSA256... </div>
                    <div class="col-8"> <input class="form-control" type="file" id="RSA256" name="RSA256"> </div>
                </div>
                <div  class="row ml-2"><div class="col-4"></div>
                    <div id="DIV_RSA256" class="col-8">
                        <input id="PASS_RSA256" type="text" name="PASS_RSA256" placeholder="Пароль от ключа RSA256" class="form-control form-control-orange">
                    </div>
                </div>
            </div>
            <div id="OBJECT_GOSTKNCA">
               <div class="row mt-3 ml-2">
                   <div class="col-4 mt-1"><label> Выберите ЭЦП ключ GOSTKNCA... </label></div>
                   <div class="col-8"> <input class="form-control" type="file" id="GOSTKNCA" name="GOSTKNCA"> </div>
               </div>
               <div class="row ml-2"><div class="col-4"></div>
                   <div id="DIV_GOSTKNCA" class="col-8">
                   <input id="PASS_GOSTKNCA" type="text" name="PASS_GOSTKNCA" placeholder="Пароль от ключа GOSTKNCA" class="form-control form-control-orange">
                   </div>
               </div>
           </div>
            <div id="OBJECT_GOSTKNCA_CER" style="display: none">
                <div class="row mt-3 ml-2">
                    <div class="col-4 mt-1"><label> Выберите сертификат GOSTKNCA.CER </label></div>
                    <div class="col-8"> <input class="form-control" type="file" id="GOSTKNCA.CER" name="GOSTKNCA.CER"> </div>
                </div>
            </div>


            <hr>
            <div class='d-flex justify-content-end text-black btnP' >
                <button class="btn btn-outline-dark textHover"> Сохранить </button>
            </div>
        </form>
    </div>

    <script>

        let  $message = ''

        let ID_BIN_value = '{{$organizationID}}';
        let organization = @json($organization);

        if (ID_BIN_value != ''){
            window.document.getElementById('organizationID').value = ID_BIN_value
            window.document.getElementById('PASS_ESF').value = '{{$PASS_ESF}}'
            window.document.getElementById('PASS_AUTH_RSA256').value = '{{$AUTH_RSA256}}'
            window.document.getElementById('PASS_RSA256').value = '{{$RSA256}}'

            let GOSTKNCA = '{{$GOSTKNCA}}'
            if (GOSTKNCA === ''){
                window.document.getElementById('OBJECT_GOSTKNCA_CER').style.display = 'block'
                window.document.getElementById('OBJECT_GOSTKNCA').style.display = 'none'
                window.document.getElementById('PASS_GOSTKNCA').value = ''
                $message = 'TRUE'
            }

            alertViewByColorName("success", "Настройки уже сохранены, вы можете внести изменения в настройки")
            ID_BIN(ID_BIN_value)
            View('PASS_ESF', 'fa_yes_ID_BIN', 'fa_no_ID_BIN')
        } else {
            window.document.getElementById('organizationID').value = organization[0].id
            ID_BIN(organization[0].id)
        }


        function ID_BIN(value){
            for (let index = 0; index < organization.length; index++){
                if (value === organization[index].id){
                    if (organization[index].hasOwnProperty('inn')){
                        window.document.getElementById('ID_BIN').innerText = organization[index].inn
                        window.document.getElementById('OBJECT_AUTH_RSA256').style.display = 'block'
                        window.document.getElementById('OBJECT_RSA256').style.display = 'block'

                        if ($message != "") {
                            window.document.getElementById('OBJECT_GOSTKNCA').style.display = 'none'
                        } else {
                            window.document.getElementById('OBJECT_GOSTKNCA').style.display = 'block'
                        }

                    }
                    else {
                        window.document.getElementById('OBJECT_AUTH_RSA256').style.display = 'none'
                        window.document.getElementById('OBJECT_RSA256').style.display = 'none'
                        window.document.getElementById('OBJECT_GOSTKNCA').style.display = 'none'
                    }
                }
            }


        }

        function PASS_OR_MATCH(value) {
            if (value){
                window.document.getElementById('DIV_AUTH_RSA256').style.display = 'none'
                window.document.getElementById('DIV_RSA256').style.display = 'none'
                window.document.getElementById('DIV_GOSTKNCA').style.display = 'none'
            } else {
                window.document.getElementById('DIV_AUTH_RSA256').style.display = 'block'
                window.document.getElementById('DIV_RSA256').style.display = 'block'
                window.document.getElementById('DIV_GOSTKNCA').style.display = 'block'
            }
        }

        @isset($message)
            $message = "{{$message}}"
        const words = $message.split(" ")
        const foundWord = words.find(word => word === "GOSTKNCA")
        if (foundWord){
            window.document.getElementById('OBJECT_GOSTKNCA_CER').style.display = 'block'
            window.document.getElementById('OBJECT_GOSTKNCA').style.display = 'none'
        }else {
            window.document.getElementById('OBJECT_GOSTKNCA_CER').style.display = 'none'
            window.document.getElementById('OBJECT_GOSTKNCA').style.display = 'block'
        }


        @endisset


        function View(object, yes, no){
            let classObject = window.document.getElementById(object).value
            console.log(classObject)
            if (classObject == ''){
                window.document.getElementById(no).style.display = 'none'
                window.document.getElementById(yes).style.display = 'contents'
            } else {
                window.document.getElementById(no).style.display = 'contents'
                window.document.getElementById(yes).style.display = 'none'
            }

        }

    </script>


@endsection

