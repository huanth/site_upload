<?php include '../header.php'; ?>

<?php if (isset($_SESSION['user'])) :
    if ($_SESSION['user']['role'] == 1) : ?>
        <section class="mb-6">
            <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2">
                QUẢN TRỊ HỆ THỐNG
            </h2>
            <div class="bg-white p-6 rounded-lg shadow-md mx-auto border border-gray-100">

                <a href="/admin/files">
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors duration-300 mb-4 hover:shadow-md">
                        <div class="flex items-center w-full">
                            <div class="bg-blue-50 p-3 rounded-full border border-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 11-4 0m2 0v6m-2-6V6a2 2 0 00-2-2H8a2 2 0 00-2 2v10a2 2 0 002 2h4a2 2 0 002-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-grow">
                                <p class="text-gray-700 font-medium">📂 Tổng số tập tin</p>
                                <p class="text-2xl font-semibold text-blue-600">
                                    2 <span class="text-gray-400 text-lg">|</span>
                                    <span class="text-blue-500">148.11 KB</span>
                                </p>
                            </div>
                            <div class="bg-blue-50 rounded-full p-2 hidden md:block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="/admin/users">
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-green-300 transition-colors duration-300 hover:shadow-md">
                        <div class="flex items-center w-full">
                            <div class="bg-green-50 p-3 rounded-full border border-green-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4.992 4.992 0 006 21h12a4.992 4.992 0 00.879-3.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 flex-grow">
                                <p class="text-gray-700 font-medium">👥 Tổng thành viên</p>
                                <p class="text-2xl font-semibold text-green-600">
                                    4 <span class="text-sm text-gray-500 ml-2">thành viên</span>
                                </p>
                            </div>
                            <div class="bg-green-50 rounded-full p-2 hidden md:block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="/admin/settings">
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-red-300 transition-colors duration-300 hover:shadow-md">
                        <div class="flex items-center w-full">
                            <div class="bg-red-50 p-3 rounded-full border border-red-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600" style="width: 40px; height: 40px;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1">
                                    <path d="M512 661.994667q61.994667 0 106.005333-44.010667t44.010667-106.005333-44.010667-106.005333-106.005333-44.010667-106.005333 44.010667-44.010667 106.005333 44.010667 106.005333 106.005333 44.010667zM829.994667 554.005333l90.005333 69.994667q13.994667 10.005333 4.010667 28.010667l-85.994667 148.010667q-8 13.994667-26.005333 8l-106.005333-42.005333q-42.005333 29.994667-72 42.005333l-16 112q-4.010667 18.005333-20.010667 18.005333l-172.010667 0q-16 0-20.010667-18.005333l-16-112q-37.994667-16-72-42.005333l-106.005333 42.005333q-18.005333 5.994667-26.005333-8l-85.994667-148.010667q-10.005333-18.005333 4.010667-28.010667l90.005333-69.994667q-2.005333-13.994667-2.005333-42.005333t2.005333-42.005333l-90.005333-69.994667q-13.994667-10.005333-4.010667-28.010667l85.994667-148.010667q8-13.994667 26.005333-8l106.005333 42.005333q42.005333-29.994667 72-42.005333l16-112q4.010667-18.005333 20.010667-18.005333l172.010667 0q16 0 20.010667 18.005333l16 112q37.994667 16 72 42.005333l106.005333-42.005333q18.005333-5.994667 26.005333 8l85.994667 148.010667q10.005333 18.005333-4.010667 28.010667l-90.005333 69.994667q2.005333 13.994667 2.005333 42.005333t-2.005333 42.005333z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-grow">
                                <p class="text-gray-700 font-medium">Cài đặt chung</p>
                                <span class="text-sm text-gray-500">Cài đặt chung của site</span>

                            </div>
                            <div class="bg-green-50 rounded-full p-2 hidden md:block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
        </section>

    <?php else : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">Bạn không có quyền truy cập trang này.</span>
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
        <strong class="font-bold">Lỗi!</strong>
        <span class="block sm:inline">Bạn chưa đăng nhập.</span>
    </div>

    <a href="/login" class="bg-primary text-white px-4 py-2 rounded-lg">Đăng nhập</a>
<?php endif; ?>

<?php include '../footer.php'; ?>