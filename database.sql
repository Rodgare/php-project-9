CREATE TABLE
    IF NOT EXISTS urls (
        id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
        name varchar(255) NOT NULL UNIQUE,
        created_at TIMESTAMP WITHOUT TIME ZONE
    );

CREATE TABLE
    IF NOT EXISTS url_checks (
        id BIGINT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
        url_id BIGINT NOT NULL,
        status_code SMALLINT,
        h1 varchar(255),
        title varchar(255),
        description varchar(255),
        created_at TIMESTAMP WITHOUT TIME ZONE
    );