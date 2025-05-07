

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
      <table id="item-table" class="border-collapse border border-gray-400 w-full">
        <thead>
          <tr>
            <th class="border border-gray-300">Term</th>
            <th class="border border-gray-300">Definition</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <tr>
            <td class="border border-gray-300">
              <input type="text" name="term[]" class="w-full p-2 border border-gray-300" required />
            </td>
            <td class="border border-gray-300">
              <input type="text" name="definition[]" class="w-full p-2 border border-gray-300" required />
            </td>
          </tr>
        </tbody>
      </table>

      <button type="button" onclick="addRow()" class="mt-4 w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Add Row
      </button>

      <button type="submit" class="mt-2 w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
        Submit
      </button>
    </form>
  </div>

  <script>
    function addRow() {
      const tableBody = document.getElementById('table-body');
      const row = document.createElement('tr');

      row.innerHTML = `
        <td class="border border-gray-300">
          <input type="text" name="term[]" class="w-full p-2 border border-gray-300" required />
        </td>
        <td class="border border-gray-300">
          <input type="text" name="definition[]" class="w-full p-2 border border-gray-300" required />
        </td>
      `;

      tableBody.appendChild(row);
    }
  </script>
</body>
</html>


