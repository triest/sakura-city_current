@extends('layouts.blog3', ['title' => 'История платежей'])

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
            <th scope="col">id платежа</th>
            <th scope="col">Дата</th>
            <th scope="col">Адрес пользователя</th>
            <th scope="col">Сумма платежа</th>
            <th scope="col">id операции Яндекс деньги</th>
        </tr>
        </thead>
        <tbody>
        @foreach($history as $item)

            <tr>
                <td><b>{{$item->id}}</b></td>
                <td><b>{{$item->date}}</b></td>
                <td><b>{{$item->user_email}}</b></td>
                <td><b>{{$item->received}}</b></td>
                <td><b>{{$item->operation_id}}</b></td>
            </tr>
        @endforeach
        </tbody>
    </table>




    <?php echo $history->render(); ?>
@endsection