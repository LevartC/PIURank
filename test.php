
<html>
<head>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
    $("#searchInput").autocomplete({
        source : function( request, response ) {
            $.ajax({
                type: 'post',
                url: "searchfile.php",
                data: {"c_title" : $("#searchInput").val()},
                dataType: "json",
                //data: {"param":"param"},
                success: function(data) {
                    console.log(data);
                    //서버에서 json 데이터 response 후 목록에 추가
                    response(
                        $.map(data, function(item) {    //json[i] 번째 에 있는게 item 임.
                            switch(item["c_type"]) {
                                case "1":
                                    mode = "S";
                                    break;
                                case "2":
                                    mode = "D";
                                    break;
                                case "3":
                                    mode = "SP";
                                    break;
                                case "4":
                                    mode = "DP";
                                    break;
                                default:
                                    break;
                            }
                            return {
                                label: mode + item["c_level"] + " : " + item["c_title"],    //UI 에서 보여지는 글자, 실제 검색어랑 비교 대상
                                value: item["c_title"],    //그냥 사용자 설정값?
                                type : item["c_type"],
                                level : item["c_level"]
                            }
                        })
                    );
                }
            });
            },    // source 는 자동 완성 대상
        select : function(event, ui) {    //아이템 선택시
            console.log(ui);//사용자가 오토컴플릿이 만들어준 목록에서 선택을 하면 반환되는 객체
            console.log(ui.item.label);    //김치 볶음밥label
            console.log(ui.item.value);    //김치 볶음밥
            console.log(ui.item.test);    //김치 볶음밥test
            
        },
        focus : function(event, ui) {    //포커스 가면
            return false;//한글 에러 잡기용도로 사용됨
        },
        minLength: 1,// 최소 글자수
        autoFocus: false, //첫번째 항목 자동 포커스 기본값 false
        classes: {    //잘 모르겠음
            "ui-autocomplete": "highlight"
        },
        delay: 200,    //검색창에 글자 써지고 나서 autocomplete 창 뜰 때 까지 딜레이 시간(ms)
//            disabled: true, //자동완성 기능 끄기
        position: { my : "left top" },    //잘 모르겠음
        close : function(event){    //자동완성창 닫아질때 호출
            console.log(event);
        }
    });
});
    </script>
</head>
<body>
    <!-- body 부분 -->
    <input id="searchInput">
</body>
</html>