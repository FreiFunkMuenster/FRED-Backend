
{{$logs->links()}}

<br>

<table style="width: 100%">

    <thead>
        <tr>
            <th>id</th> <th>tag</th> <th>message</th> <th>level</th> <th>time</th> <th>user</th>
        </tr>
    </thead>

    <tbody>

        @foreach($logs as $log)

        <tr>
            <td>
                {{$log->id}}
            </td>
            <td>
                {{$log->tag}}
            </td>
            <td>
                {{$log->message}}
            </td>
            <td>
                {{$log->level}}
            </td>
            <td>
                {{$log->time}}
            </td>
            <td>
                {{$log->app_user_id}}
            </td>
        </tr>

        @endforeach

    </tbody>

</table>


