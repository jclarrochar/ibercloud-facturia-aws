# outputs.tf

output "vpc_id" {
  description = "ID de la VPC creada"
  value       = aws_vpc.main.id
}

output "vpc_cidr" {
  description = "Bloque CIDR de la VPC"
  value       = aws_vpc.main.cidr_block
}

output "public_subnet_ids" {
  description = "IDs de las subredes publicas"
  value       = aws_subnet.public[*].id
}

output "private_app_subnet_ids" {
  description = "IDs de las subredes privadas de aplicacion"
  value       = aws_subnet.private_app[*].id
}

output "private_db_subnet_ids" {
  description = "IDs de las subredes privadas de base de datos"
  value       = aws_subnet.private_db[*].id
}

output "alb_security_group_id" {
  description = "ID del Security Group del ALB"
  value       = aws_security_group.alb.id
}

output "app_security_group_id" {
  description = "ID del Security Group de la capa de aplicacion"
  value       = aws_security_group.app.id
}

output "db_security_group_id" {
  description = "ID del Security Group de la base de datos"
  value       = aws_security_group.db.id
}