DROP TABLE IF EXISTS "user_favorite_article";
DROP TABLE IF EXISTS "article_view";
DROP TABLE IF EXISTS "article";

CREATE TABLE "article"
(
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    title        VARCHAR(128) NOT NULL,
    slug         VARCHAR(128) NOT NULL UNIQUE,
    author       VARCHAR(50)  NOT NULL,
    text         TEXT         NULL,
    published_at DATETIME     NOT NULL
);

CREATE TABLE "article_view"
(
    article_id INTEGER  NOT NULL,
    created_at DATETIME NOT NULL,
    CONSTRAINT "fk__article_view-article_id" FOREIGN KEY (article_id) REFERENCES article (id)
);

CREATE TABLE "user_favorite_article"
(
    user       VARCHAR(50) NOT NULL,
    article_id INTEGER     NOT NULL,
    PRIMARY KEY (user, article_id),
    CONSTRAINT "fk__user_favorite_article-article_id" FOREIGN KEY (article_id) REFERENCES article (id)
);
