<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Create Document</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#ffffff;
    font-family:Arial,sans-serif;
    padding:40px;
}

.create-box{
    width:100%;
    max-width:460px;
    margin:auto;
    background:white;
    padding:40px;
    border-radius:18px;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
}

.create-title{
    font-size:32px;
    font-weight:bold;
    margin-bottom:30px;
    color:#111827;
}

.input-box{
    width:100%;
    padding:12px 14px;
    border:1px solid #d1d5db;
    border-radius:12px;
    font-size:18px;
    margin-bottom:20px;
    outline:none;
}

.input-box:focus{
    border-color:#4f46e5;
}

.create-btn{
    padding:10px 16px;
    border:none;
    background:#4f46e5;
    color:white;
    border-radius:12px;
    cursor:pointer;
    font-size:16px;
    font-weight:bold;
}

.create-btn:hover{
    background:#4338ca;
}

</style>

</head>

<body>

<div class="create-box">

    <h1 class="create-title">
        Create Document
    </h1>

    <form
        method="POST"
        action="/documents/store"
    >

        @csrf

        <input
            type="text"
            name="title"
            placeholder="Document title"
            class="input-box"
            required
        >

        <button
            type="submit"
            class="create-btn"
        >
            Create Document
        </button>

    </form>

</div>

</body>
</html>