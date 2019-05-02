
{{$logs->links()}}

<br>

<table>

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
        </tr>
        <tr>
            <td>
                {{$log->tag}}
            </td>
        </tr>
        <tr>
            <td>
                {{$log->message}}
            </td>
        </tr>
        <tr>
            <td>
                {{$log->level}}
            </td>
        </tr>
        <tr>
            <td>
                {{$log->time}}
            </td>
        </tr>
        <tr>
            <td>
                {{$log->app_user_id}}
            </td>
        </tr>

        @endforeach

    </tbody>

</table>


