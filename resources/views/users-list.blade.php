@extends('layouts.blog3', ['title' => 'Список пользователей'])

@section('content')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
    </script>

    <table id="example" class="table table-striped table-bordered">
        <thead>
        <tr>
            <th scope="col">id анкеты</th>
            <th scope="col">Имя</th>
            <th scope="col">email</th>
            <th scope="col">Анкета</th>
            <th scope="col">Заблокирован</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $item)

            <tr>
                <td><b>{{$item->id}}</b></td>
                <td><b>{{$item->name}}</b></td>
                <td><b>{{$item->email}}</b></td>


                @if($girl=$item->anketisExsisUser($item->id)!=null)
                    <td><b>
                        <!-- {{$girl=$item->anketisExsisUser($item->id)}} -->

                            <a href="{{route('girlsShowAuchAnket')}}">
                                <a href="{{route('showGirl',['id'=>$girl->id])}}">{{$girl->name}}</a>
                            </a><br>
                        </b></td>
                @else
                    <td><b>
                            Нет анкеты
                        </b></td>
                @endif
                @if($girl=$item->anketisExsisUser($item->id)!=null)
                    @if($gir=$item->anketisExsisUser($item->id)->banned==1)
                        <td><b>
                                Заблокирован
                            </b></td>
                    @endif
                @else
                    <td><b>

                        </b></td>
                @endif


            </tr>
        @endforeach
        </tbody>
    </table>




    <?php echo $users->render(); ?>
@endsection