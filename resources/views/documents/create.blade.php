<!DOCTYPE html>
<html>
<head>

    <title>Create Document</title>

</head>

<body>

<h1>Create Document</h1>

<form
    method="POST"
    action="/documents/store"
>

    @csrf

    <input
        type="text"
        name="title"
        placeholder="Document title"
    >

    <button type="submit">
        Create
    </button>

</form>

</body>
</html>