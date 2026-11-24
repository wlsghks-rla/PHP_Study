# PHP_Study — 갤러리 게시판 · 설문조사 (순수 PHP)

프레임워크 없이 PHP로 만든 학습용 프로젝트입니다.
해시태그/갤러리 게시판, 첨부파일, 설문조사, 메뉴 권한 관리, 통계(CSV 포함)를 구현했습니다.

## 폴더 구조

```
public_html/
├─ index.php                          단일 진입점 (/{폴더}/{파일} → 컨트롤러)
├─ application/
│   ├─ controller/{폴더}/{파일}.php   URL 기준으로 분류 (예: /board/list)
│   ├─ model/                         게시판·설문·통계 로직
│   ├─ core/                          include_once_path, db, auth, view 등 공통 함수
│   └─ config/                        config.php, schema.sql, install.php
├─ view/{폴더}/{파일}.php             컨트롤러와 같은 이름의 화면
├─ css/  js/  image/                  정적 파일 (image/ = 에디터 업로드, 저장소에는 비어 있음)
└─ storage/                           SQLite DB, 첨부 원본 (저장소에는 비어 있음)
```

## 실행 환경
- PHP 8.1 이상 (확장: `pdo_sqlite`, `mbstring`, `fileinfo`)

## 실행 방법
```bash
cd public_html
php application/config/install.php       # DB 생성 + 샘플 데이터 + 초기 계정 출력
php -S 127.0.0.1:8000 index.php          # http://127.0.0.1:8000 접속
```
- `install.php`는 초기 비밀번호를 **실행할 때마다 무작위로 생성해 한 번만 출력**합니다.
- 직접 지정하려면 `php application/config/install.php 관리자비번 일반회원비번` 으로 실행하세요.
- 초기 계정은 `admin`(슈퍼관리자), `user1`(일반회원)이며, 로그인 후 변경하세요.

## 주요 기능
- 회원가입(아이디 정규식·중복확인), 로그인, 슈퍼관리자/일반회원/비회원 권한
- 게시판: 페이징(5/10/50), 검색(전체·제목·내용·작성자), 답글, 댓글·대댓글, 좋아요, 공지(기간 지정), 다중 삭제, 조회수(아이디별 하루 1회)
- 갤러리: 이미지 최대 5개(10MB, jpg·jpeg·gif·webp·svg), 대표 썸네일 선택
- 해시태그(최대 5개) 클릭 검색, 첨부파일(UUID 저장·원본명 다운로드·다운로드 이력)
- 설문조사: 5문항 페이징, 필수/없음/기타 검증, 진행률, 관리자 문항·통계 관리
- 메뉴 관리(일반형·갤러리형·링크형), 메뉴 접근·게시글 조회수 통계, CSV 다운로드, 통합 검색

## 보안 관련
- 모든 POST 요청에 CSRF 토큰 검사, 세션 쿠키 `HttpOnly`/`SameSite=Lax`
- 비밀번호는 `password_hash`로 저장, SQL은 모두 prepared statement
- 본문 HTML은 허용 태그만 남기고 `on*`/`javascript:` 제거, 출력은 `htmlspecialchars` 처리
- 첨부파일은 웹 루트 밖이 아닌 `storage/`에 UUID 이름으로 저장하고, 다운로드는 권한 검사를 거치는 컨트롤러로만 제공
- **운영 배포 시**: `storage/`를 웹 루트 밖으로 옮기고(`UPLOAD_DIR`, `DB_FILE` 수정), HTTPS를 사용하세요.
