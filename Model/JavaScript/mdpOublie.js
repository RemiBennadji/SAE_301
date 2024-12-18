const B2 = document.getElementById("start")


function showCode(){
    var B1 = document.getElementById("resend")
    var input = document.getElementById("inputCode")
    var acpt = document.getElementById("accept")
    B1.style.display = "block";
    input.style.display = "block";
    acpt.style.display = "block";
}

B2.addEventListener("click",function (){
    showCode();
});
document.getElementById('MdpOublieForm').addEventListener('submit', function (event) {
    event.preventDefault();
});
