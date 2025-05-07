

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup</title>
    <link rel="stylesheet" href="./output.css">
</head>
<body class="flex items-start justify-center min-h-screen">
    <div class="p-6 rounded-lg shadow-xl w-screen">
        <h1 class="text-xl font-semibold text-center text-gray-700">Add items</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="mt-4">
            <div class='flex flex-row gap-4'>
            <div class="mb-3 w-full">
                <label class="block text-gray-600 font-medium">Term</label>
                <input type="text" name="user_id" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
            </div>
            <div class="mb-3 w-full">
                <label class="block text-gray-600 font-medium">Definition</label>
                <input type="password" name="password" class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required />
            </div>
</div>
            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-10">
                Submit
            </button>
        </form>
    </div>
</body>

</html>

