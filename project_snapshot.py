#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import os
import re
import json
from typing import Dict, List, Any, Set

class ProjectScanner:
    def __init__(self, base_dir: str):
        self.base_dir = base_dir
        self.exclude_dirs = {'.git', 'vendor', 'node_modules', 'writable', 'public/assets'}
        self.php_file_pattern = re.compile(r'\.php$')
        self.class_pattern = re.compile(r'class\s+(\w+)(?:\s+extends\s+(\w+))?(?:\s+implements\s+([\w\s,]+))?')
        self.function_pattern = re.compile(r'(?:public|private|protected|static)?\s*function\s+(\w+)\s*\(([^)]*)\)')
        self.method_comment_pattern = re.compile(r'/\*\*\s*(.*?)\s*\*/', re.DOTALL)
        self.route_pattern = re.compile(r'\$routes->(\w+)\([\'"]([^\'"]+)[\'"],\s*[\'"]?([^\'"]+)[\'"]?\);')
        self.dependency_pattern = re.compile(r'use\s+([\w\\]+)(?:\s+as\s+(\w+))?;')
        
    def scan_project(self) -> Dict[str, Any]:
        """掃描整個專案並生成專案結構的字典表示"""
        project_data = {
            "meta": {
                "project_name": os.path.basename(self.base_dir),
                "scan_timestamp": self._get_timestamp()
            },
            "modules": [],
            "controllers": [],
            "models": [],
            "routes": [],
            "services": [],
            "libraries": [],
            "hooks": [],
            "config": {},
            "dependencies": set()
        }
        
        for root, dirs, files in os.walk(self.base_dir):
            # 排除不需要的目錄
            dirs[:] = [d for d in dirs if d not in self.exclude_dirs]
            
            for file in files:
                if not self.php_file_pattern.search(file):
                    continue
                
                file_path = os.path.join(root, file)
                rel_path = os.path.relpath(file_path, self.base_dir)
                
                # 檢查檔案類型並處理
                if 'app/Controllers' in rel_path:
                    controller_data = self._extract_class_info(file_path, 'controller')
                    if controller_data:
                        project_data["controllers"].append(controller_data)
                        
                elif 'app/Models' in rel_path:
                    model_data = self._extract_class_info(file_path, 'model')
                    if model_data:
                        project_data["models"].append(model_data)
                        
                elif 'app/Config/Routes.php' in rel_path:
                    routes = self._extract_routes(file_path)
                    if routes:
                        project_data["routes"].extend(routes)
                        
                elif 'app/Services' in rel_path:
                    service_data = self._extract_class_info(file_path, 'service')
                    if service_data:
                        project_data["services"].append(service_data)
                        
                elif 'app/Libraries' in rel_path:
                    library_data = self._extract_class_info(file_path, 'library')
                    if library_data:
                        project_data["libraries"].append(library_data)
                        
                elif 'app/Config' in rel_path:
                    config_name = os.path.splitext(file)[0]
                    config_data = self._extract_config_data(file_path)
                    if config_data:
                        project_data["config"][config_name] = config_data
                        
                elif 'app/Commands' in rel_path:
                    command_data = self._extract_class_info(file_path, 'command')
                    if command_data:
                        if "commands" not in project_data:
                            project_data["commands"] = []
                        project_data["commands"].append(command_data)
                        
                elif 'app/Database/Migrations' in rel_path:
                    migration_data = self._extract_class_info(file_path, 'migration')
                    if migration_data:
                        if "migrations" not in project_data:
                            project_data["migrations"] = []
                        project_data["migrations"].append(migration_data)
                
                # 提取所有檔案中的依賴關係
                dependencies = self._extract_dependencies(file_path)
                if dependencies:
                    project_data["dependencies"].update(dependencies)
        
        # 把set轉成list，以便序列化成JSON
        project_data["dependencies"] = list(project_data["dependencies"])
        
        return project_data
    
    def _extract_class_info(self, file_path: str, type_name: str) -> Dict[str, Any]:
        """提取檔案中的類別資訊"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            class_matches = self.class_pattern.findall(content)
            if not class_matches:
                return None
            
            class_data = {
                "type": type_name,
                "file": os.path.relpath(file_path, self.base_dir),
                "name": class_matches[0][0],
                "extends": class_matches[0][1] if class_matches[0][1] else None,
                "implements": [i.strip() for i in class_matches[0][2].split(',')] if class_matches[0][2] else [],
                "methods": self._extract_methods(content),
                "dependencies": list(self._extract_dependencies(file_path))
            }
            
            return class_data
            
        except Exception as e:
            print(f"錯誤: 處理檔案 {file_path} 時出現異常: {e}")
            return None
    
    def _extract_methods(self, content: str) -> List[Dict[str, Any]]:
        """提取類別中的方法"""
        methods = []
        
        function_matches = self.function_pattern.findall(content)
        for match in function_matches:
            method_name = match[0]
            parameters = match[1].strip()
            
            # 嘗試找出方法的註解
            # 這是一個簡化的方式，實際的註解提取可能需要更複雜的邏輯
            comment = ""
            method_pos = content.find(f"function {method_name}")
            if method_pos > 0:
                # 向前查找最近的註解
                comment_section = content[:method_pos].strip()
                comment_match = self.method_comment_pattern.search(comment_section[::-1])
                if comment_match:
                    comment = comment_match.group(1).strip()
            
            methods.append({
                "name": method_name,
                "parameters": [p.strip() for p in parameters.split(',') if p.strip()],
                "description": comment
            })
        
        return methods
    
    def _extract_routes(self, file_path: str) -> List[Dict[str, str]]:
        """提取路由定義"""
        routes = []
        
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            route_matches = self.route_pattern.findall(content)
            for match in route_matches:
                http_method = match[0]
                url = match[1]
                handler = match[2]
                
                routes.append({
                    "method": http_method,
                    "url": url,
                    "handler": handler
                })
            
            return routes
            
        except Exception as e:
            print(f"錯誤: 處理路由檔案 {file_path} 時出現異常: {e}")
            return []
    
    def _extract_config_data(self, file_path: str) -> Dict[str, Any]:
        """提取設定檔資料"""
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # 這裡的處理非常簡化，實際上解析PHP設定檔需要更複雜的邏輯
            # 我們只提取一些關鍵字和值作為指標
            config_data = {
                "file": os.path.relpath(file_path, self.base_dir),
                "has_database_config": 'database' in content.lower(),
                "has_email_config": 'email' in content.lower(),
                "has_app_config": 'app' in content.lower(),
                "has_cache_config": 'cache' in content.lower(),
                "has_security_config": 'security' in content.lower()
            }
            
            return config_data
            
        except Exception as e:
            print(f"錯誤: 處理設定檔 {file_path} 時出現異常: {e}")
            return {}
    
    def _extract_dependencies(self, file_path: str) -> Set[str]:
        """提取檔案中的依賴關係"""
        dependencies = set()
        
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            matches = self.dependency_pattern.findall(content)
            for match in matches:
                dependency = match[0]
                dependencies.add(dependency)
            
            return dependencies
            
        except Exception as e:
            print(f"錯誤: 處理檔案 {file_path} 提取依賴時出現異常: {e}")
            return set()
    
    def _get_timestamp(self) -> str:
        """取得當前時間戳記"""
        import datetime
        return datetime.datetime.now().isoformat()

    def save_to_json(self, output_path: str = None) -> None:
        """儲存專案資料到JSON檔案"""
        if output_path is None:
            output_path = os.path.join(self.base_dir, 'project_snapshot.json')
        
        project_data = self.scan_project()
        
        # 移除空項目，減少檔案大小
        for key in list(project_data.keys()):
            if not project_data[key] and key != "meta":
                del project_data[key]
        
        with open(output_path, 'w', encoding='utf-8') as f:
            json.dump(project_data, f, indent=2)
        
        print(f"專案快照已儲存至: {output_path}")

if __name__ == "__main__":
    # 取得專案目錄（假設當前目錄就是專案目錄）
    project_dir = os.path.dirname(os.path.abspath(__file__))
    
    # 建立掃描器並執行掃描
    scanner = ProjectScanner(project_dir)
    scanner.save_to_json()
    
    print("掃描完成！")
