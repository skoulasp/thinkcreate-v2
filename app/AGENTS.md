# AI Development Rules for This Repository

This repository follows a disciplined, incremental development style.
AI must act as an assistant, not an architect unless explicitly instructed.

---

## 1. General Constraints

- Do not install new packages unless explicitly instructed.
- Do not refactor unrelated files.
- Modify only the files explicitly requested.
- Prefer minimal changes over convenience abstractions.
- Avoid large architectural rewrites.
- Do not introduce Tailwind.
- Prefer SCSS.
- Keep admin and public layers separated.
- Do not convert working patterns into alternative patterns without justification.
- Do not introduce new architectural patterns (e.g., repositories, service layers) unless explicitly requested.
- If unsure about intent, ask clarifying questions before proceeding.

---

## 2. Database & Migrations

- Never modify existing migrations once they have been committed.
- Always create new migrations for schema changes.
- Do not assume migrations have been run.
- When adding columns, define sensible defaults.
- Respect model casting when adding new attributes.
- Avoid destructive schema operations unless explicitly instructed.
- Treat schema changes as production-sensitive operations.

---

## 3. Code Style & Architecture

- Use conventional Laravel structure.
- Keep controllers thin.
- Prefer Form Requests for validation when appropriate.
- Keep Blade templates simple and semantic.
- Avoid clever abstractions.
- Prefer explicit logic over magic.
- Favor readability over compactness.
- Use Gates and Policies idiomatically.
- Separate concerns clearly between DB, Controller, View, and Policy layers.

---

## 4. AI Output Requirements

- Output full file contents when modifying a file.
- Do not partially patch files.
- Do not modify multiple layers (DB + Controller + View) unless explicitly instructed.
- If multiple layers are required, clearly separate them in the output.
- Briefly explain non-trivial architectural decisions.
- Do not assume implicit context.
- If architectural alternatives exist, propose options with trade-offs instead of choosing automatically.

---

## 5. Workflow & Git Discipline

- Respect clear commit boundaries.
- Suggest commit messages when appropriate.
- Avoid mixing unrelated changes in one suggestion.
- Prefer incremental implementation over large feature drops.
- Treat this as a production-grade codebase, not a tutorial sandbox.
- Preserve clean diff visibility for every change.

---

## Philosophy

- AI accelerates implementation, not decision-making.
- Structure is decided deliberately.
- Simplicity is preferred over premature abstraction.
- Every change should be understandable without hidden magic.
