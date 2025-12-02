<?php
session_start();
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "smart_wallet";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM incomes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
<!-- navbar -->
 <nav class="bg-blue-500/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
          <div class="shrink-0">
            <img src="../assets/img/logo.png" alt="logo" class="size-8" />
          </div>
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
              <a href="../index.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">Home</a>
              <a href="../dashboard.php" aria-current="page" class="rounded-md px-3 py-2 text-sm text-gray-300 font-medium hover:bg-white/5 hover:text-white">Dashboard</a>
              <a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">Incomes</a>
              <a href="../expenses/list.php" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">Expenses</a>
            </div>
          </div>
        </div>
        <div class="hidden md:block">
          <div class="ml-4 flex items-center md:ml-6">
            <button type="button" class="relative rounded-full p-1 text-gray-400 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
              <span class="absolute -inset-1.5"></span>
              <span class="sr-only">View notifications</span>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>
          </div>
        </div>
        <div class="-mr-2 flex md:hidden">
          <button id="menu_btn" type="button" command="--toggle" commandfor="mobile_menu" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
            <span class="absolute -inset-0.5"></span>
            <span class="sr-only">Open main menu</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
              <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
              <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div id="mobile_menu" hidden class="block md:hidden">
      <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
        <a href="../index.php" aria-current="page" class="block rounded-md bg-gray-950/50 px-3 py-2 text-base font-medium text-white">Home</a>
        <a href="../dashboard.php" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Dashboard</a>
        <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Incomes</a>
        <a href="../expenses/list.php" class="block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Expenses</a>
      </div>
    </div>
  </nav>
<!-- Formulaire d'ajouter un income -->
<form class="max-w-md mx-auto mb-2" action = "../incomes/create.php" method="post">
  <div class="relative z-0 w-full mb-5 group">
      <input type="text" name="income" id="income" class="block py-2.5 px-0 w-full text-sm text-heading bg-transparent border-0 border-b-2 border-default-medium appearance-none focus:outline-none focus:ring-0 focus:border-brand peer" placeholder=" " required />
      <label for="income" class="absolute text-sm text-body duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-fg-brand peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">Income</label>
  </div>
  <div class="relative z-0 w-full mb-5 group">
      <input type="date" name="dateIn" id="dateIn" class="block py-2.5 px-0 w-full text-sm text-heading bg-transparent border-0 border-b-2 border-default-medium appearance-none focus:outline-none focus:ring-0 focus:border-brand peer" placeholder=" " required />
      <label for="dateIn" class="absolute text-sm text-body duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-fg-brand peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto"></label>
  </div>
    <div class="relative z-0 w-full mb-5 group">
        <input type="text" name="descriptionIn" id="descriptionIn" class="block py-2.5 px-0 w-full text-sm text-heading bg-transparent border-0 border-b-2 border-default-medium appearance-none focus:outline-none focus:ring-0 focus:border-brand peer" placeholder=" " required />
        <label for="descriptionIn" class="absolute text-sm text-body duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-fg-brand peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">Description</label>
    </div>
  <button type="submit" class="text-white rounded-sm bg-blue-500 box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none">Submit</button>
</form>

<!-- Afficher list des incomes -->
<div class="relative overflow-x-auto bg-blue-500/50 shadow-xs rounded-base border border-default">
    <table class="w-full text-sm text-left rtl:text-right text-body">
        <thead class="text-sm text-body bg-neutral-secondary-medium border-b border-default-medium">
            <tr>
                <th scope="col" class="p-4">
                    <div class="flex items-center">
                        <input id="table-checkbox" type="checkbox" value="" class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                        <label for="table-checkbox" class="sr-only">Table checkbox</label>
                    </div>
                </th>
                <th scope="col" class="px-6 py-3 font-medium">ID</th>
                <th scope="col" class="px-6 py-3 font-medium">Incomes</th>
                <th scope="col" class="px-6 py-3 font-medium">Date</th>
                <th scope="col" class="px-6 py-3 font-medium">Description</th>
                <th scope="col" class="px-6 py-3 font-medium">Modify</th>
                <th scope="col" class="px-6 py-3 font-medium">Delete</th>
            </tr>
        </thead>
        <tbody>
            <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                <td class="w-4 p-4">
                    <div class="flex items-center">
                        <input id="table-checkbox-2" type="checkbox" value="" class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                        <label for="table-checkbox-2" class="sr-only">Table checkbox</label>
                    </div>
                </td>
                <?php if($result && $result->num_rows >0){
                    while($row = $result->fetch_assoc()){
                ?>
                    <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap"><?php echo htmlspecialchars($row['idIn'])?></th>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['amountIn'])?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['dateIn'])?></td>
                    <td class="px-6 py-4"><?php echo htmlspecialchars($row['descriptionIn'])?></td>
                    <td class="flex items-center px-6 py-4"><a href="#" class="font-medium text-blue-500 hover:underline">Edit</a></td>
                    <td><a href="#" class="font-medium text-red-500 hover:underline ms-3">Remove</a></td>
                <?php
                }
                }
                ?>
            </tr>
        </tbody>
    </table>
</div>
<script src="../assets/js/main.js"></script>
</body>
</html>