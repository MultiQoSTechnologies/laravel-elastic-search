<!DOCTYPE html>
<html>
<head>
    <title>ElasticSearch Demo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
          integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ=="
          crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css">

</head>
<body>
<div class="container">
    <div style="margin-top: 20px;">
        <button class="btn btn-sm"><a href="{{ route('article.index') }}">Reset</a></button>
        <button class="btn btn-sm"><a href="{{ route('article.create') }}">Create</a></button>
        <div style="float:left;margin-right:25px;">
            <form method="get" action="{{ route('article.index') }}">
                <input type="text" name="search" value="{{ $keyword }}">
                <button type="submit" class="btn btn-sm" value="submit">Go</button>
            </form>

        </div>
    </div>


    <table class="table table-bordered table-striped table-hover" style="margin-top:15px;" id="example">
        <thead class="table-light">
        <tr>
            <th>Article ID</th>
            <th>Title</th>
            <th>Body</th>
            <th>Tags</th>
        </tr>
        </thead>
        <tbody>
        @foreach($articles as $article)
            <tr>
                <td>{{ $article->id }}</td>
                <td>{{ $article->title }}</td>
                <td>{{ $article->body }}</td>
                <td>{{ $article->tags }}</td>
            </tr>
        @endforeach
        </tbody>

    </table>
    {{ $articles->appends(['q' => $keyword])->links() }}
</div>

<br/><br/>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>

</body>
</html>
