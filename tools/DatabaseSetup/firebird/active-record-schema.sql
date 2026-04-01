-- Firebird Test tables for the active record module


RECREATE TABLE persons (
    id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    PRIMARY KEY  (id)
);

RECREATE TABLE children (
   id INTEGER NOT NULL GENERATED ALWAYS AS IDENTITY (START WITH 1 INCREMENT BY 1),
    person_id INT NOT NULL,
    name_first VARCHAR(100) NOT NULL,
    name_last VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    PRIMARY KEY (id)
);