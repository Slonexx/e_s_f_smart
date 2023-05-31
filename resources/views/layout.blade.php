<!doctype html>
<html lang="en">
    @include('head')
    <body style="background-color:#dcdcdc;">

           <div class="page headfull">
                <div class="sidenav">
                    <div class="p-2 gradient_invert pb-3 ">

                       <div style="font-size: 12px"> <b> ИНФОРМАЦИОННАЯ СИСТЕМА ЭЛЕКТРОННЫХ СЧЕТОВ-ФАКТУР </b> </div>
                       <div style="font-size: 9px"> Комитет государственных доходов </div>
                       <div style="font-size: 9px"> Министерства финансов Республики Казахстан </div>



                    </div>
                    <br>
                    <a id="link_1" href="/{{$accountId}}?isAdmin={{ request()->isAdmin }}">Главная </a>
                    @if ( request()->isAdmin == null )
                    @else
                    @if( request()->isAdmin == 'ALL')
                            <button id="btn_1" class="mt-1 dropdown-btn">Настройки <i class="fa fa-caret-down"></i> </button>
                            <div class="dropdown-container">
                                <a id="link_3" class="mt-1" href="/Setting/createAuthToken/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Основные </a>
                                <a id="link_5" class="mt-1" href="/Setting/Worker/{{$accountId}}?isAdmin={{ request()->isAdmin }}"> Доступ </a>
                            </div>
                        @endif
                    @endif
                    <button class="mt-1 dropdown-btn"> Помощь <i class="fa fa-caret-down"></i> </button>
                    <div class="dropdown-container">
                        <a target="_blank" href="https://smartwebkassa.bitrix24.site/contact/">
                            <i class="fa-solid fa-address-book"></i> Контакты </a>
                        <a target="_blank" href="https://api.whatsapp.com/send/?phone=77232400545&text=" >
                            <i class="fa-brands fa-whatsapp"></i> Написать на WhatsApp </a>
                        <a target="_blank" href="https://smartwebkassa.bitrix24.site/instruktsiiponastroyke_fflj" >
                            <i class="fa-solid fa-chalkboard-user"></i> Инструкция </a>
                    </div>
                </div>
           </div>
           <div class="main head-full" style=""> @yield('content') </div>
    </body>
</html>

<script>

        let item = '@yield('item')'

        window.document.getElementById(item).classList.add('active_sprint')
        if (item.replace(/[^+\d]/g, '') > 1 && item.replace(/[^+\d]/g, '') <= 5){
           this_click(window.document.getElementById('btn_1'))
        }

        function this_click(btn){
            btn.classList.toggle("active");
            let dropdownContent = btn.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        }

</script>

@include('style')
@include('script')

