<!DOCTYPE html>
<html>
<head>

    <title>Documents</title>

</head>

<body style="
    font-family:Arial;
    padding:40px;
    background:#f3f4f6;
">

<h1>
    All Documents
</h1>

<a
    href="/documents/create"
    style="
        display:inline-block;
        margin-bottom:20px;
        padding:10px 16px;
        background:#2563eb;
        color:white;
        text-decoration:none;
        border-radius:8px;
    "
>
    New Document
</a>

@foreach($documents as $document)

    <div style="
        background:white;
        padding:20px;
        margin-bottom:16px;
        border-radius:10px;
    ">

        <h3>
            {{ $document->title }}
        </h3>

        <a
            href="/documents/{{ $document->id }}"
        >
            Open Document
        </a>

    </div>

@endforeach

</body>
</html>